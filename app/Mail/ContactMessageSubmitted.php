<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('رسالة جديدة من نموذج اتصل بنا - ' . $this->contactMessage->subject_label)
            ->view('emails.contact-message');
    }
}
