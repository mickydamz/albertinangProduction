<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\Size;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin product taxonomy CRUD — tags, sizes and colors
 * (AdminTagController / AdminSizeController / AdminColorController).
 */
class AdminTaxonomyCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ── Tags ────────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_and_delete_a_tag()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/tags', ['name' => 'Energy Saving'])
            ->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['name' => 'Energy Saving']);

        $tag = Tag::where('name', 'Energy Saving')->first();
        $this->actingAs($admin)->delete("/admin/tags/{$tag->id}")
            ->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /** @test */
    public function tag_names_must_be_unique()
    {
        Tag::create(['name' => 'Inverter', 'slug' => 'inverter']);

        $this->actingAs($this->admin())->from('/admin/tags/create')
            ->post('/admin/tags', ['name' => 'Inverter'])
            ->assertSessionHasErrors('name');
    }

    // ── Sizes ───────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_and_delete_a_size()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/sizes', ['name' => '55 inch'])
            ->assertRedirect(route('admin.sizes.index'));
        $this->assertDatabaseHas('sizes', ['name' => '55 inch']);

        $size = Size::where('name', '55 inch')->first();
        $this->actingAs($admin)->delete("/admin/sizes/{$size->id}")->assertRedirect();
        $this->assertDatabaseMissing('sizes', ['id' => $size->id]);
    }

    // ── Colors ──────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_and_delete_a_color()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/colors', ['name' => 'Matte Black'])
            ->assertRedirect(route('admin.colors.index'));
        $this->assertDatabaseHas('colors', ['name' => 'Matte Black']);

        $color = Color::where('name', 'Matte Black')->first();
        $this->actingAs($admin)->delete("/admin/colors/{$color->id}")->assertRedirect();
        $this->assertDatabaseMissing('colors', ['id' => $color->id]);
    }
}
