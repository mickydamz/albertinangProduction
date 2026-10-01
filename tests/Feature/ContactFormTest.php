<?php

namespace Tests\Feature;

use App\Mail\ContactAutoReply;
use App\Mail\ContactMessageReceived;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Public contact form (ContactController, routes contact / contact.store).
 *
 * A visitor submits the form → we validate, screen the Cloudflare Turnstile
 * CAPTCHA + a honeypot, dedupe rapid re-submits, and fire an admin notification
 * plus a customer auto-reply. Both mailables implement ShouldQueue, so they are
 * asserted on the queue. Turnstile's remote verify call is faked via Http.
 */
class ContactFormTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();
        Cache::flush(); // clear dedupe fingerprints between tests
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function passTurnstile(): void
    {
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true], 200)]);
    }

    private function failTurnstile(): void
    {
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false], 200)]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'Jane Buyer',
            'email'                 => 'jane@example.com',
            'phone'                 => '08012345678',
            'userMessage'           => 'Do you deliver to Enugu?',
            'cf-turnstile-response' => 'dummy-token',
        ], $overrides);
    }

    // ── Rendering ──────────────────────────────────────────────────────────────

    /** @test */
    public function the_contact_page_renders()
    {
        $this->get('/contact')->assertOk()->assertViewIs('sims.contact');
    }

    // ── Happy path ─────────────────────────────────────────────────────────────

    /** @test */
    public function a_valid_submission_sends_the_admin_and_auto_reply_emails()
    {
        $this->passTurnstile();

        $this->from('/contact')->post('/contact', $this->payload())
            ->assertRedirect('/contact')
            ->assertSessionHas('success');

        Mail::assertQueued(ContactMessageReceived::class, 1);
        Mail::assertQueued(ContactAutoReply::class, fn ($m) => $m->hasTo('jane@example.com'));
    }

    // ── Validation ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_requires_name_email_and_message()
    {
        // No turnstile token sent → the Turnstile rule isn't invoked (no HTTP).
        $this->from('/contact')->post('/contact', [])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'email', 'userMessage']);

        Mail::assertNothingQueued();
    }

    /** @test */
    public function it_rejects_an_invalid_email()
    {
        $this->passTurnstile();

        $this->from('/contact')->post('/contact', $this->payload(['email' => 'not-an-email']))
            ->assertSessionHasErrors('email');

        Mail::assertNothingQueued();
    }

    /** @test */
    public function a_failed_captcha_is_rejected()
    {
        $this->failTurnstile();

        $this->from('/contact')->post('/contact', $this->payload())
            ->assertSessionHasErrors('cf-turnstile-response');

        Mail::assertNothingQueued();
    }

    // ── Anti-abuse ─────────────────────────────────────────────────────────────

    /** @test */
    public function the_honeypot_silently_drops_bot_submissions()
    {
        // A filled 'website' field = bot. The controller fakes success and sends
        // nothing (short-circuits before validation, so no CAPTCHA call needed).
        $this->from('/contact')->post('/contact', $this->payload(['website' => 'http://spam.example']))
            ->assertRedirect('/contact')
            ->assertSessionHas('success');

        Mail::assertNothingQueued();
    }

    /** @test */
    public function a_duplicate_submission_within_the_window_is_not_resent()
    {
        $this->passTurnstile();
        $payload = $this->payload();

        $this->from('/contact')->post('/contact', $payload)->assertRedirect('/contact');
        $this->from('/contact')->post('/contact', $payload)->assertRedirect('/contact');

        // The dedupe fingerprint means only the first submission dispatched mail.
        Mail::assertQueued(ContactMessageReceived::class, 1);
        Mail::assertQueued(ContactAutoReply::class, 1);
    }
}
