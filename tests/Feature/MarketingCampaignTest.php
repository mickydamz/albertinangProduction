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
        (require database_path('migrations/2026_10_15_000001_create_marketing_campaigns.php'))->up();
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
}
