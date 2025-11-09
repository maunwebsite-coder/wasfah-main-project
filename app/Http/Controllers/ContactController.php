<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageSubmitted;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function sendMessage(Request $request)
    {
        $subjectKeys = implode(',', array_keys(ContactMessage::SUBJECT_LABELS));

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => "required|string|in:{$subjectKeys}",
            'message' => 'required|string|max:2000',
            'source' => 'nullable|string|max:100',
        ], [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'subject.required' => 'الموضوع مطلوب',
            'subject.in' => 'الرجاء اختيار موضوع صالح من القائمة.',
            'message.required' => 'الرسالة مطلوبة',
            'message.max' => 'الرسالة طويلة جداً (الحد الأقصى 2000 حرف)',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $submissionSource = $data['source'] ?? $request->route()?->getName() ?? 'contact.page';

        $contactMessage = ContactMessage::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'subject_label' => ContactMessage::SUBJECT_LABELS[$data['subject']] ?? $data['subject'],
            'message' => $data['message'],
            'status' => ContactMessage::STATUS_PENDING,
            'meta' => array_filter([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'source' => $submissionSource,
            ]),
        ]);

        $this->notifyTeam($contactMessage);

        return back()->with('success', 'تم تسجيل رسالتك بنجاح وسيتواصل فريق وصفة معك فور مراجعتها.');
    }

    /**
     * Attempt to email the support team; log failures without interrupting the user.
     */
    protected function notifyTeam(ContactMessage $contactMessage): void
    {
        $recipient = config('contact.recipient', config('mail.from.address'));

        if (empty($recipient)) {
            Log::warning('Contact form recipient is not configured.', [
                'contact_message_id' => $contactMessage->id,
            ]);

            return;
        }

        try {
            Mail::mailer('failover')
                ->to($recipient)
                ->send(
                    (new ContactMessageSubmitted($contactMessage))
                        ->replyTo($contactMessage->email, $contactMessage->full_name)
                );

            $contactMessage->update(['status' => ContactMessage::STATUS_NOTIFIED]);
        } catch (\Throwable $th) {
            Log::error('Failed to deliver contact form submission via email.', [
                'contact_message_id' => $contactMessage->id,
                'error' => $th->getMessage(),
            ]);

            $contactMessage->update([
                'meta' => array_merge($contactMessage->meta ?? [], [
                    'mail_error' => $th->getMessage(),
                ]),
            ]);
        }
    }
}
