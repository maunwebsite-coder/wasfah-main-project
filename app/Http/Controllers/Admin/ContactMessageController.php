<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display the list of contact messages with basic filtering.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $subject = $request->query('subject', 'all');
        $search = trim((string) $request->query('search', ''));
        $perPage = (int) $request->query('per_page', 25);

        if ($perPage < 10 || $perPage > 100) {
            $perPage = 25;
        }

        $messagesQuery = ContactMessage::query()->latest();

        if ($status !== 'all' && in_array($status, [ContactMessage::STATUS_PENDING, ContactMessage::STATUS_NOTIFIED], true)) {
            $messagesQuery->where('status', $status);
        }

        if ($subject !== 'all' && in_array($subject, ContactMessage::subjectKeys(), true)) {
            $messagesQuery->where('subject', $subject);
        }

        if ($search !== '') {
            $messagesQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $messagesQuery->paginate($perPage)->withQueryString();

        $selectedMessage = null;
        if ($request->filled('message')) {
            $selectedMessage = ContactMessage::find($request->integer('message'));
        }

        $statusCounts = ContactMessage::selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->toArray();

        $statusTotals = [
            'all' => ContactMessage::count(),
            ContactMessage::STATUS_PENDING => $statusCounts[ContactMessage::STATUS_PENDING] ?? 0,
            ContactMessage::STATUS_NOTIFIED => $statusCounts[ContactMessage::STATUS_NOTIFIED] ?? 0,
        ];

        return view('admin.contact-messages.index', [
            'messages' => $messages,
            'selectedMessage' => $selectedMessage,
            'status' => $status,
            'subjectFilter' => $subject,
            'searchTerm' => $search,
            'statusTotals' => $statusTotals,
            'perPage' => $perPage,
            'subjectOptions' => ContactMessage::subjectLabelOptions(),
        ]);
    }
}
