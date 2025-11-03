<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $name;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $code)
    {
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('رمز التحقق من البريد الإلكتروني - وصفة')
            ->view('emails.register-verification-code');
    }
}

