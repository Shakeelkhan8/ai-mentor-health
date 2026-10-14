<?php

namespace Tests\Feature\Security;

use App\Mail\NewsLetterMail;
use App\Models\ContactForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicFormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_newsletter_signup_sends_to_a_valid_address(): void
    {
        $this->from('/')
            ->post(route('sendmail'), ['email' => 'reader@example.com'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        Mail::assertSent(NewsLetterMail::class, fn (NewsLetterMail $mail) => $mail->hasTo('reader@example.com'));
    }

    public function test_newsletter_signup_rejects_an_invalid_address(): void
    {
        $this->from('/')
            ->post(route('sendmail'), ['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_newsletter_signup_is_rate_limited(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('sendmail'), ['email' => "reader{$i}@example.com"])->assertRedirect();
        }

        $this->post(route('sendmail'), ['email' => 'reader6@example.com'])->assertStatus(429);

        Mail::assertSentCount(5);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $data = ['name' => 'Visitor', 'phone' => '0300 1234567', 'email' => 'visitor@example.com', 'message' => 'Hello'];

        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('contact.store'), $data)->assertRedirect();
        }

        $this->post(route('contact.store'), $data)->assertStatus(429);

        $this->assertSame(5, ContactForm::query()->count());
    }
}
