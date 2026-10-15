<?php

namespace Tests\Feature;

use App\Mail\MarketingPromotion;
use App\Models\MarketingCampaign;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MarketingCampaignTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Each test owns an in-memory database, never a staging/customer database.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'marketing.enabled' => true]);
        \DB::purge('sqlite');
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('role')->default('user');
            $t->timestamps();
        });
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->boolean('is_active');
            $t->unsignedBigInteger('parent_id')->nullable();
        });
        Schema::create('brands', function (Blueprint $t) {
            $t->id();
            $t->boolean('is_active');
            $t->string('name');
            $t->string('slug');
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });
        \App\Models\Setting::clearCache();
        foreach (['store_name' => 'AlbertinaNG', 'store_address' => 'Office from settings', 'store_phone' => '08064066170', 'store_phone_country_code' => '+234', 'store_email' => 'contact@example.test'] as $key => $value) {
            \App\Models\Setting::set($key, $value);
        }
        (require database_path('migrations/2026_10_15_000001_create_marketing_campaigns.php'))->up();
        (require database_path('migrations/2026_10_15_000002_expand_marketing_campaigns.php'))->up();
        config(['marketing.mailer' => 'smtp', 'mail.mailers.smtp.transport' => 'smtp']);
        Mail::fake();
    }

    private function user($role = 'user', $subscribed = false)
    {
        return User::forceCreate(['name' => 'Demo Customer', 'email' => uniqid().'@example.test', 'password' => bcrypt('password'), 'role' => $role, 'marketing_subscribed_at' => $subscribed ? now() : null]);
    }

    private function campaign()
    {
        return MarketingCampaign::create(['subject' => 'Sale', 'message' => 'Save on your next order.']);
    }

    public function test_counts_include_staff_consent_but_only_customers_are_eligible()
    {
        $admin = $this->user('admin', true);
        $customer = $this->user('user', true);
        $this->user('user', false);
        $this->actingAs($admin)->get('/admin/marketing?subscriber_counts=1')
            ->assertOk()->assertExactJson(['total' => 2, 'customers' => 1]);
        $this->get('/admin/marketing')->assertOk()->assertSee($customer->email)
            ->assertSee('Staff account')->assertSee('2</span> people opted in', false);
        $this->actingAs($customer)->post('/account/email-preferences', [])->assertRedirect();
        $this->actingAs($admin)->get('/admin/marketing?subscriber_counts=1')
            ->assertExactJson(['total' => 1, 'customers' => 0]);
        $this->actingAs($customer)->get('/admin/marketing?subscriber_counts=1')->assertForbidden();
    }

    public function test_offer_button_positions_persist_and_render_with_centre_default()
    {
        $this->actingAs($this->user('admin'));
        foreach (['left', 'center', 'right'] as $position) {
            $this->post('/admin/marketing', ['subject' => 'Position', 'button_alignment' => $position])->assertRedirect();
            $campaign = MarketingCampaign::latest('id')->first();
            $this->assertSame($position, $campaign->brand['button_alignment']);
            $this->get(route('admin.marketing.preview', $campaign))->assertOk()->assertSee('align="'.$position.'" cellspacing="0"', false);
        }
        $campaign = $this->campaign();
        $this->get(route('admin.marketing.preview', $campaign))->assertSee('align="center" cellspacing="0"', false);
        $this->post('/admin/marketing', ['subject' => 'Bad', 'button_alignment' => 'absolute'])->assertSessionHasErrors('button_alignment');
    }

    public function test_registration_consent_is_optional_and_saved_only_when_checked()
    {
        Schema::create('countries', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('iso_code'); });
        Schema::create('cities', function (Blueprint $t) { $t->id(); $t->unsignedBigInteger('country_id'); $t->string('name'); });
        Schema::table('users', function (Blueprint $t) {
            foreach (['country', 'state', 'postal_code', 'shipping_address', 'city'] as $field) $t->string($field)->nullable();
            $t->unsignedBigInteger('country_id')->nullable(); $t->unsignedBigInteger('state_id')->nullable();
        });
        \DB::table('countries')->insert(['id' => 1, 'name' => 'Nigeria', 'iso_code' => 'NG']);
        $controller = new class extends \App\Http\Controllers\Auth\RegisterController {
            public function validateData($data) { return $this->validator($data); }
            public function createUser($data) { return $this->create($data); }
        };
        foreach ([null, '0', '1'] as $consent) {
            $data = ['name' => 'Registration Demo', 'email' => uniqid().'@example.test', 'password' => 'password123', 'password_confirmation' => 'password123', 'country_id' => 1];
            if ($consent !== null) $data['marketing_opt_in'] = $consent;
            $this->assertFalse($controller->validateData($data)->fails());
            $user = $controller->createUser($data);
            $this->assertSame($consent === '1', $user->fresh()->marketing_subscribed_at !== null);
        }
        $data['marketing_opt_in'] = 'unexpected';
        $this->assertTrue($controller->validateData($data)->fails());
        $this->get('/register')->assertOk()->assertSee('name="marketing_opt_in" value="1"', false);
    }

    public function test_admin_campaign_pages_render()
    {
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->get(route('admin.marketing.index'))->assertOk()->assertSee('Create a promotion');
        $this->get(route('admin.marketing.show', $campaign))->assertOk()->assertSee('Edit draft');
    }

    public function test_only_admins_can_create_campaigns()
    {
        $this->post('/admin/marketing', [])->assertRedirect('/login');
        $this->actingAs($this->user())->post('/admin/marketing', [])->assertForbidden();
        $this->actingAs($this->user('admin'))->post('/admin/marketing', ['subject' => 'Sale', 'message' => 'Offer', 'offer_url' => 'javascript:alert(1)'])->assertSessionHasErrors('offer_url');
        $this->post('/admin/marketing', ['subject' => 'Sale', 'message' => 'Offer', 'offer_url' => 'https://example.test/offers'])->assertRedirect();
        $this->assertDatabaseCount('marketing_campaigns', 1);
    }

    public function test_only_opted_in_customers_are_queued_and_duplicate_sends_are_rejected()
    {
        $admin = $this->user('admin', true);
        $customer = $this->user('user', true);
        $this->user();
        $this->user('supplier', true);
        $campaign = $this->campaign();
        $this->actingAs($admin)->post(route('admin.marketing.send', $campaign), ['confirm' => 1])->assertRedirect();
        $this->assertDatabaseCount('marketing_deliveries', 1);
        $this->assertDatabaseHas('marketing_deliveries', ['user_id' => $customer->id]);
        $this->post(route('admin.marketing.send', $campaign), ['confirm' => 1])->assertStatus(409);
        $this->artisan('marketing:send')->assertSuccessful();
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertSent(MarketingPromotion::class, 1);
        $this->assertDatabaseHas('marketing_campaigns', ['id' => $campaign->id, 'status' => 'completed']);
    }

    public function test_unsubscribe_requires_signature_and_confirmation_and_suppresses_pending_email()
    {
        $customer = $this->user('user', true);
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1]);
        $this->get('/marketing/unsubscribe/'.$customer->id)->assertForbidden();
        $url = URL::signedRoute('marketing.unsubscribe', ['user' => $customer->id]);
        $this->get($url)->assertOk();
        $this->assertNotNull($customer->fresh()->marketing_subscribed_at);
        $this->post($url)->assertOk();
        $this->assertNull($customer->fresh()->marketing_subscribed_at);
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertNothingSent();
        $this->assertDatabaseHas('marketing_deliveries', ['user_id' => $customer->id, 'status' => 'skipped']);
    }

    public function test_customer_can_opt_in_and_out_of_own_account()
    {
        $customer = $this->user();
        $this->actingAs($customer)->post(route('marketing.preferences.update'), ['subscribe' => 1])->assertRedirect();
        $this->assertNotNull($customer->fresh()->marketing_subscribed_at);
        $this->post(route('marketing.preferences.update'), [])->assertRedirect();
        $this->assertNull($customer->fresh()->marketing_subscribed_at);
    }

    public function test_disabled_environment_blocks_tests_and_campaign_dispatch()
    {
        $campaign = $this->campaign();
        $this->user('user', true);
        config(['marketing.enabled' => false]);
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.test', $campaign))->assertStatus(422);
        $this->post(route('admin.marketing.send', $campaign), ['confirm' => 1])->assertStatus(422);
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_test_email_is_sent_only_to_current_admin_and_email_content_is_escaped()
    {
        $admin = $this->user('admin');
        $campaign = MarketingCampaign::create(['subject' => '<script>Sale</script>', 'message' => '<img src=x onerror=alert(1)>']);
        $this->actingAs($admin)->get(route('admin.marketing.preview', $campaign))->assertOk()->assertDontSee('<img src=x', false)->assertSee('&lt;img', false);
        $this->post(route('admin.marketing.test', $campaign), ['email' => 'someone@example.test'])->assertRedirect();
        Mail::assertSent(MarketingPromotion::class, fn ($mail) => $mail->hasTo($admin->email));
        $this->assertDatabaseCount('marketing_deliveries', 0);
    }

    public function test_transport_failure_is_recorded_without_automatic_resend()
    {
        $this->user('user', true);
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1]);
        Mail::shouldReceive('mailer')->with('smtp')->once()->andReturnSelf();
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Transport unavailable'));
        $this->artisan('marketing:send')->assertSuccessful();
        $this->artisan('marketing:send')->assertSuccessful();
        $this->assertDatabaseHas('marketing_deliveries', ['status' => 'failed']);
    }

    public function test_drafts_can_be_edited_but_queued_campaigns_are_immutable()
    {
        $campaign = $this->campaign();
        $this->user('user', true);
        $this->actingAs($this->user('admin'))->put(route('admin.marketing.update', $campaign), ['subject' => 'Updated sale', 'message' => 'New offer'])->assertRedirect();
        $this->assertSame('Updated sale', $campaign->fresh()->subject);
        $this->post(route('admin.marketing.send', $campaign), ['confirm' => 1])->assertRedirect();
        $this->put(route('admin.marketing.update', $campaign), ['subject' => 'Changed', 'message' => 'Offer'])->assertStatus(409);
    }

    public function test_changed_email_is_skipped()
    {
        $customer = $this->user('user', true);
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1]);
        $customer->update(['email' => 'changed@example.test']);
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertNothingSent();
        $this->assertDatabaseHas('marketing_deliveries', ['status' => 'skipped']);
    }

    public function test_rich_text_is_sanitized_and_preserves_safe_formatting()
    {
        $this->actingAs($this->user('admin'))->post('/admin/marketing', [
            'subject' => 'Rich sale', 'body_html' => '<h2 style="color:#177855;background:url(javascript:bad)">Sale</h2><script>alert(1)</script><p onclick="alert(2)"><strong>Save today</strong> <a href="javascript:alert(3)">unsafe</a><a href="https://example.test">Shop</a><img src=x onerror=alert(4)></p>',
        ])->assertRedirect();
        $campaign = MarketingCampaign::first();
        $this->assertStringContainsString('<strong>Save today</strong>', $campaign->body_html);
        $this->assertStringContainsString('color:#177855', $campaign->body_html);
        foreach (['<script', 'onclick', 'javascript:', '<img', 'background:'] as $bad) {
            $this->assertStringNotContainsString($bad, $campaign->body_html);
        }
        $this->assertStringContainsString('Save today', $campaign->message);
    }

    public function test_images_are_validated_stored_removed_and_included_with_branding()
    {
        \Storage::fake('public');
        $this->actingAs($this->user('admin'))->post('/admin/marketing', [
            'subject' => 'Picture offer', 'body_html' => '<p>Our latest offers</p>',
            'images' => [\Illuminate\Http\UploadedFile::fake()->image('offer.jpg', 640, 300)], 'image_alt' => ['Our offer banner'],
        ])->assertRedirect();
        $campaign = MarketingCampaign::first();
        $this->assertCount(1, $campaign->images);
        \Storage::disk('public')->assertExists($campaign->images[0]['path']);
        $this->get(route('admin.marketing.preview', $campaign))->assertOk()->assertSee('Our offer banner')->assertSee('contact@example.test');
        $this->put(route('admin.marketing.update', $campaign), ['subject' => 'Picture offer', 'body_html' => '<p>Updated offer</p>', 'remove_images' => [0]])->assertRedirect();
        $this->assertCount(0, $campaign->fresh()->images);
        $this->post('/admin/marketing', ['subject' => 'Bad image', 'message' => 'Offer', 'images' => [\Illuminate\Http\UploadedFile::fake()->create('script.svg', 20, 'image/svg+xml')]])->assertSessionHasErrors('images.0');
    }

    public function test_future_campaigns_wait_until_due_and_pause_resume_cancel_work()
    {
        $this->user('user', true);
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1, 'scheduled_at' => now()->addDays(2)->timezone('Europe/London')->format('Y-m-d\\TH:i')])->assertRedirect();
        $this->assertSame('scheduled', $campaign->fresh()->status);
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertNothingSent();
        $this->post(route('admin.marketing.control', $campaign), ['action' => 'pause'])->assertRedirect();
        $this->assertSame('paused', $campaign->fresh()->status);
        $this->post(route('admin.marketing.control', $campaign), ['action' => 'resume'])->assertRedirect();
        $this->assertSame('scheduled', $campaign->fresh()->status);
        $this->post(route('admin.marketing.control', $campaign), ['action' => 'cancel'])->assertRedirect();
        $this->assertSame('cancelled', $campaign->fresh()->status);
        $this->assertDatabaseHas('marketing_deliveries', ['status' => 'cancelled']);
        $this->travel(3)->days();
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_due_scheduled_campaign_is_delivered_and_recent_audience_filters_old_opt_ins()
    {
        $old = $this->user('user', true);
        $old->forceFill(['marketing_subscribed_at' => now()->subDays(60)])->save();
        $recent = $this->user('user', true);
        $campaign = $this->campaign();
        $campaign->update(['audience' => 'recent']);
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1, 'scheduled_at' => now()->addDay()->timezone('Europe/London')->format('Y-m-d\\TH:i')])->assertRedirect();
        $this->assertDatabaseCount('marketing_deliveries', 1);
        $this->travel(2)->days();
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertSent(MarketingPromotion::class, fn ($mail) => $mail->hasTo($recent->email));
    }

    public function test_selected_audience_excludes_unsubscribed_users_and_copy_has_no_deliveries()
    {
        $selected = $this->user('user', true);
        $excluded = $this->user();
        $this->user('user', true);
        $campaign = $this->campaign();
        $campaign->update(['audience' => 'selected', 'selected_users' => [$selected->id, $excluded->id]]);
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1])->assertRedirect();
        $this->assertDatabaseCount('marketing_deliveries', 1);
        $this->post(route('admin.marketing.duplicate', $campaign))->assertRedirect();
        $copy = MarketingCampaign::latest('id')->first();
        $this->assertSame('draft', $copy->status);
        $this->assertSame(0, $copy->deliveries()->count());
    }

    public function test_click_tracking_requires_signature_and_records_only_sent_delivery()
    {
        $this->user('user', true);
        $campaign = $this->campaign();
        $campaign->update(['offer_url' => 'https://example.test/sale']);
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1]);
        $delivery = $campaign->deliveries()->first();
        $url = URL::signedRoute('marketing.click', ['delivery' => $delivery->id]);
        $this->get('/marketing/offer/'.$delivery->id)->assertForbidden();
        $this->get($url)->assertRedirect('https://example.test/sale');
        $this->assertSame(0, $delivery->fresh()->click_count);
        $this->artisan('marketing:send')->assertSuccessful();
        $this->get($url)->assertRedirect('https://example.test/sale');
        $this->assertSame(1, $delivery->fresh()->click_count);
        $this->assertNotNull($delivery->fresh()->clicked_at);
        $this->get(route('admin.marketing.export', $campaign))->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_promotional_email_has_official_sender_reply_address_text_fallback_and_unsubscribe_header()
    {
        Mail::swap($this->app->make('mail.manager'));
        $mail = new MarketingPromotion($this->campaign(), $this->user());
        $mail->build();
        $this->assertSame('promotions@albertinang.com', $mail->from[0]['address']);
        $this->assertSame('promotions@albertinang.com', $mail->replyTo[0]['address']);
        $this->assertSame('marketing.plain', $mail->textView);
        $message = new \Symfony\Component\Mime\Email();
        foreach ($mail->callbacks as $callback) {
            $callback($message);
        }
        $this->assertNotNull($message->getHeaders()->get('List-Unsubscribe'));
    }

    public function test_batch_limits_and_log_mailer_cannot_report_a_campaign_as_sent()
    {
        $this->user('user', true);
        $this->user('user', true);
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1]);
        config(['marketing.batch_limit' => 1]);
        $this->artisan('marketing:send')->assertSuccessful();
        Mail::assertSent(MarketingPromotion::class, 1);
        $this->assertDatabaseHas('marketing_deliveries', ['status' => 'pending']);
        config(['mail.mailers.smtp.transport' => 'log']);
        $this->artisan('marketing:send')->assertExitCode(1);
        Mail::assertSent(MarketingPromotion::class, 1);
    }

    public function test_provider_one_click_unsubscribe_requires_valid_signed_link_and_records_campaign()
    {
        $customer = $this->user('user', true);
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1]);
        $delivery = $campaign->deliveries()->first();
        $this->post('/marketing/unsubscribe-one-click/'.$customer->id, ['List-Unsubscribe' => 'One-Click'])->assertForbidden();
        $url = URL::signedRoute('marketing.unsubscribe.oneclick', ['user' => $customer->id, 'delivery' => $delivery->id]);
        $this->post($url, ['List-Unsubscribe' => 'One-Click'])->assertNoContent();
        $this->assertNull($customer->fresh()->marketing_subscribed_at);
        $this->assertNotNull($delivery->fresh()->unsubscribed_at);
    }

    public function test_schedule_rejects_past_dates_and_empty_selected_audiences()
    {
        $campaign = $this->campaign();
        $this->actingAs($this->user('admin'))->post(route('admin.marketing.send', $campaign), ['confirm' => 1, 'scheduled_at' => '2020-01-01T10:00'])->assertSessionHasErrors('scheduled_at');
        $this->post('/admin/marketing', ['subject' => 'Sale', 'message' => 'Offer', 'audience' => 'selected'])->assertSessionHasErrors('selected_users');
        $this->assertDatabaseCount('marketing_deliveries', 0);
    }

    public function test_sent_email_embeds_logo_and_uploaded_picture_without_requiring_public_image_access()
    {
        \Storage::fake('public');
        $picture = \Illuminate\Http\UploadedFile::fake()->image('banner.png')->store('marketing', 'public');
        $campaign = $this->campaign();
        $campaign->update(['images' => [['path' => $picture, 'alt' => 'Offer picture']]]);
        $mail = new MarketingPromotion($campaign, $this->user());
        $mime = new \Symfony\Component\Mime\Email();
        $message = new \Illuminate\Mail\Message($mime);
        $html = view('marketing.email', ['message' => $message] + $mail->viewData())->render();
        $this->assertStringContainsString('cid:', $html);
        $this->assertCount(2, $mime->getAttachments());
        $this->assertSame('', \App\Services\MarketingAssets::picture('../.env', $message));
    }

    public function test_promotion_message_is_optional_and_offer_defaults_to_the_test_shop()
    {
        $this->actingAs($this->user('admin'))->post('/admin/marketing', ['subject' => 'Subject only'])->assertRedirect();
        $campaign = MarketingCampaign::first();
        $this->assertSame('', $campaign->message);
        $this->assertNull($campaign->body_html);
        $this->assertSame(url('/shop'), $campaign->offer_url);
        $this->get(route('admin.marketing.preview', $campaign))->assertOk()->assertDontSee('Subject only')->assertSee('Shop now');
    }

    public function test_existing_campaign_footer_uses_current_settings_and_does_not_repeat_subject()
    {
        $campaign = $this->campaign();
        $campaign->update(['brand' => ['name' => 'Old name', 'phone' => '0999999', 'address' => 'Old address', 'email' => 'old@example.test']]);
        $this->actingAs($this->user('admin'))->get(route('admin.marketing.preview', $campaign))->assertOk()
            ->assertSee('Office from settings')->assertSee('contact@example.test')->assertSee('+2348064066170')
            ->assertSee('tel:+2348064066170', false)->assertDontSee('Old address')->assertDontSee('<h1', false);
        \App\Models\Setting::set('store_address', 'Updated office');
        \App\Models\Setting::set('store_email', 'updated@example.test');
        $this->get(route('admin.marketing.preview', $campaign))->assertSee('Updated office')->assertSee('updated@example.test');
        $mail = new MarketingPromotion($campaign, $this->user());
        $plain = view('marketing.plain', $mail->viewData())->render();
        $this->assertStringNotContainsString($campaign->subject, $plain);
        $this->assertStringContainsString('updated@example.test', $plain);
    }

    public function test_international_contact_numbers_preserve_existing_country_codes()
    {
        $this->assertSame('+2348064066170', \App\Services\MarketingBrand::internationalPhone('0806 406 6170'));
        $this->assertSame('+2348064066170', \App\Services\MarketingBrand::internationalPhone('2348064066170'));
        $this->assertSame('+447700900123', \App\Services\MarketingBrand::internationalPhone('+44 7700 900123'));
        $this->assertSame('+447700900123', \App\Services\MarketingBrand::internationalPhone('0044 7700 900123'));
        $this->assertSame('+447700900123', \App\Services\MarketingBrand::internationalPhone('07700 900123', '+44'));
    }

    public function test_email_offer_button_has_a_real_clickable_destination_and_new_window_target()
    {
        $campaign = $this->campaign();
        $campaign->update(['offer_url' => 'https://test.albertinang.com/shop']);
        $this->actingAs($this->user('admin'))->get(route('admin.marketing.preview', $campaign))->assertOk()
            ->assertSee('href="https://test.albertinang.com/shop" target="_blank"', false);
        $campaign->update(['offer_url' => null]);
        $customer = $this->user('user', true);
        $this->post(route('admin.marketing.send', $campaign), ['confirm' => 1]);
        $delivery = $campaign->deliveries()->first();
        $this->get(URL::signedRoute('marketing.click', ['delivery' => $delivery->id]))->assertRedirect(url('/shop'));
    }

    public function test_admin_can_set_phone_country_code_and_invalid_codes_are_rejected()
    {
        $this->actingAs($this->user('admin'))->put(route('admin.settings.update'), ['store_name' => 'AlbertinaNG', 'store_phone' => '07700 900123', 'store_phone_country_code' => '+44', 'store_email' => 'office@example.test', 'store_address' => 'London office'])->assertRedirect();
        $brand = \App\Services\MarketingBrand::current();
        $this->assertSame('+447700900123', $brand['phone']);
        $this->assertSame('office@example.test', $brand['email']);
        $this->put(route('admin.settings.update'), ['store_name' => 'AlbertinaNG', 'store_phone_country_code' => 'bad'])->assertSessionHasErrors('store_phone_country_code');
    }
}
