<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display inbox of contact submissions.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query()->latest();

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($inner) use ($search) {
                $inner->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($subject = $request->query('subject')) {
            if (array_key_exists($subject, ContactMessage::SUBJECT_LABELS)) {
                $query->where('subject', $subject);
            }
        }

        if ($status = $request->query('status')) {
            if (in_array($status, ContactMessage::STATUSES, true)) {
                $query->where('status', $status);
            }
        }

        $messages = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => ContactMessage::whereIn('status', [
                ContactMessage::STATUS_PENDING,
            ])->count(),
            'unreviewed' => ContactMessage::whereIn('status', [
                ContactMessage::STATUS_PENDING,
                ContactMessage::STATUS_NOTIFIED,
            ])->count(),
            'partnership' => ContactMessage::where('subject', 'partnership')
                ->whereIn('status', [
                    ContactMessage::STATUS_PENDING,
                    ContactMessage::STATUS_NOTIFIED,
                ])
                ->count(),
            'total' => ContactMessage::count(),
        ];

        $highlightId = (int) $request->query('message', 0);

        return view('admin.contact-messages.index', [
            'messages' => $messages,
            'stats' => $stats,
            'subjectLabels' => ContactMessage::SUBJECT_LABELS,
            'statusLabels' => ContactMessage::STATUS_LABELS,
            'highlightId' => $highlightId,
        ]);
    }

    /**
     * Update the status of a contact message.
     */
    public function updateStatus(ContactMessage $contactMessage, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', ContactMessage::STATUSES),
        ]);

        $contactMessage->update([
            'status' => $validated['status'],
            'meta' => array_merge($contactMessage->meta ?? [], [
                'status_updated_by' => $request->user()->id,
                'status_updated_at' => now()->toDateTimeString(),
            ]),
        ]);

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }
}
