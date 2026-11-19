<?php

namespace Tests\Feature;

use App\Mail\NewsLetterMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The public pages and emails carry this product's own brand, served from this app.
 * The previous marketing theme hotlinked another company's logo and stock images.
 */
class BrandingTest extends TestCase
{
    use RefreshDatabase;

    private const THIRD_PARTY_HOSTS = ['squarespace-cdn.com', 'pngwing.com'];

    public function test_public_pages_use_the_product_logo_and_no_hotlinked_brand_assets(): void
    {
        foreach (['/', '/contact', '/login', '/register'] as $uri) {
            $response = $this->get($uri)->assertOk();
            $response->assertSee('images/brand/logo-mark.svg', false);
            $response->assertSee('favicon.svg', false);

            foreach (self::THIRD_PARTY_HOSTS as $host) {
                $response->assertDontSee($host, false);
            }
        }
    }

    public function test_old_section_urls_redirect_to_the_landing_page_sections(): void
    {
        $this->get('/about')->assertRedirect('/#how-it-works');
        $this->get('/services')->assertRedirect('/#features');
        $this->get('/pricing')->assertRedirect('/#pricing');
        $this->get('/faqs')->assertRedirect('/#faq');
    }

    public function test_newsletter_email_is_branded_with_the_product_name(): void
    {
        $mail = new NewsLetterMail('reader@example.com');

        $this->assertSame('Welcome to AI Mentor Health wellness tips', $mail->envelope()->subject);

        $html = $mail->render();
        $this->assertStringContainsString('AI Mentor', $html);
        $this->assertStringNotContainsString('Mind Mentor', $html);
        foreach (self::THIRD_PARTY_HOSTS as $host) {
            $this->assertStringNotContainsString($host, $html);
        }
    }

    public function test_contact_form_keeps_the_typed_message_after_a_validation_error(): void
    {
        $this->from('/contact')
            ->post(route('contact.store'), ['message' => 'Can I export my habits?'])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'phone', 'email']);

        $this->get('/contact')->assertSee('Can I export my habits?');
    }
}
