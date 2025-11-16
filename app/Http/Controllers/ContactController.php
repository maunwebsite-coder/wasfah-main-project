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
        $subjectKeys = implode(',', ContactMessage::subjectKeys());

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => "required|string|in:{$subjectKeys}",
            'message' => 'required|string|max:2000',
            'source' => 'nullable|string|max:100',
        ], $this->validationMessages());

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
            'subject_label' => ContactMessage::subjectLabel($data['subject']),
            'message' => $data['message'],
            'status' => ContactMessage::STATUS_PENDING,
            'meta' => array_filter([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'source' => $submissionSource,
            ]),
        ]);

        $this->notifyTeam($contactMessage);

        return back()->with('success', __('flash.success.contact.message_submitted'));
    }

    /**
     * Localized validation messages for the contact form.
     *
     * @return array<string, string>
     */
    protected function validationMessages(): array
    {
        $messages = __('contact.form.validation');

        $fallbacks = [
            'first_name.required' => __('validation.required', [
                'attribute' => __('contact.form.fields.first_name.label'),
            ]),
            'last_name.required' => __('validation.required', [
                'attribute' => __('contact.form.fields.last_name.label'),
            ]),
            'email.required' => __('validation.required', [
                'attribute' => __('contact.form.fields.email.label'),
            ]),
            'email.email' => __('validation.email', [
                'attribute' => __('contact.form.fields.email.label'),
            ]),
            'subject.required' => __('validation.required', [
                'attribute' => __('contact.form.fields.subject.label'),
            ]),
            'subject.in' => __('validation.in', [
                'attribute' => __('contact.form.fields.subject.label'),
            ]),
            'message.required' => __('validation.required', [
                'attribute' => __('contact.form.fields.message.label'),
            ]),
            'message.max' => __('validation.max.string', [
                'attribute' => __('contact.form.fields.message.label'),
                'max' => 2000,
            ]),
        ];

        foreach ($fallbacks as $rule => $fallback) {
            $fallbacks[$rule] = $messages[$rule] ?? $fallback;
        }

        return $fallbacks;
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
