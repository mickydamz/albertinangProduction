<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Admin storefront content management — the About and Contact pages, whose text
 * is stored as Settings (AdminAboutController / AdminContactController).
 */
class AdminContentPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Setting::clearCache();
    }

    protected function tearDown(): void
    {
        Setting::clearCache();
        parent::tearDown();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function an_admin_can_update_the_about_page_content()
    {
        $this->actingAs($this->admin())->put('/admin/about', [
            'about_hero_subtitle' => 'Nigeria\'s trusted appliance store.',
            'about_years'         => '15',
        ])->assertRedirect();

        Setting::clearCache();
        $this->assertSame("Nigeria's trusted appliance store.", Setting::get('about_hero_subtitle'));
        $this->assertSame('15', Setting::get('about_years'));
    }

    /** @test */
    public function an_admin_can_update_the_contact_page_content()
    {
        $this->actingAs($this->admin())->put('/admin/contact', [
            'contact_hero_title' => 'Get in touch',
            'contact_email'      => 'hello@albertinang.com',
        ])->assertRedirect();

        Setting::clearCache();
        $this->assertSame('Get in touch', Setting::get('contact_hero_title'));
        $this->assertSame('hello@albertinang.com', Setting::get('contact_email'));
    }

    /** @test */
    public function the_contact_email_must_be_a_valid_email()
    {
        $this->actingAs($this->admin())->from('/admin/contact')->put('/admin/contact', [
            'contact_email' => 'not-an-email',
        ])->assertSessionHasErrors('contact_email');
    }
}
