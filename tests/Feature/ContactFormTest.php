<?php

namespace Tests\Feature;

use App\Mail\ContactMessageSubmitted;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_contact_form(): void
    {
        config(['contact.recipient' => 'team@example.com']);

        Mail::fake();

        $response = $this->post(route('contact.send'), [
            'first_name' => 'Sara',
            'last_name' => 'Haddad',
            'email' => 'sara@example.com',
            'phone' => '0790000000',
            'subject' => 'general',
            'message' => 'مرحبا، أرغب في معرفة المزيد عن خدماتكم.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'sara@example.com',
            'subject' => 'general',
            'status' => ContactMessage::STATUS_NOTIFIED,
        ]);

        Mail::assertSent(ContactMessageSubmitted::class, function (ContactMessageSubmitted $mail) {
            return $mail->hasTo('team@example.com')
                && $mail->contactMessage->email === 'sara@example.com';
        });
    }
}
