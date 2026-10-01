<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Admin banner management (BannerController) — image-backed homepage banners.
 */
class AdminBannerCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Storage::fake('public');
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function an_admin_can_create_a_banner_with_an_image()
    {
        $this->actingAs($this->admin())->post('/admin/banners/store', [
            'type'   => 'banner1',
            'title'  => 'Summer Sale',
            'status' => 1,
            'image'  => UploadedFile::fake()->image('banner.jpg'),
        ])->assertRedirect();

        $this->assertDatabaseHas('banners', ['type' => 'banner1', 'title' => 'Summer Sale']);
    }

    /** @test */
    public function creating_a_banner_requires_a_type_and_image()
    {
        $this->actingAs($this->admin())->from('/admin/banners/create')
            ->post('/admin/banners/store', ['title' => 'No image'])
            ->assertSessionHasErrors(['type', 'image']);
    }

    /** @test */
    public function an_admin_can_delete_a_banner()
    {
        $banner = Banner::create(['type' => 'popup', 'title' => 'Temp', 'image' => 'banners/x.jpg', 'status' => true]);

        $this->actingAs($this->admin())->delete("/admin/banners/{$banner->id}")->assertRedirect();

        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }
}
