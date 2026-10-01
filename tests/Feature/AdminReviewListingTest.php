<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Search + pagination on the admin Reviews (ratings) index.
 */
class AdminReviewListingTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->role = 'admin';
        $u->save();
        return $u;
    }

    private function review(string $productName): Review
    {
        $product = Product::create(['name' => $productName, 'price' => 10000, 'is_active' => true]);
        $author  = User::factory()->create();
        return Review::create([
            'product_id' => $product->id,
            'user_id'    => $author->id,
            'rating'     => 5,
            'content'    => 'A review for ' . $productName,
        ]);
    }

    /** @test */
    public function reviews_index_renders_and_search_filters_by_product()
    {
        $keep = 'FindMeTV_' . uniqid();
        $drop = 'HideMeFan_' . uniqid();
        $this->review($keep);
        $this->review($drop);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.reviews.index'))->assertOk();

        $this->actingAs($admin)->get(route('admin.reviews.index', ['search' => $keep]))
            ->assertOk()
            ->assertSee($keep)
            ->assertDontSee($drop);
    }

    /** @test */
    public function reviews_index_paginates()
    {
        $tag = 'PgRev' . substr(uniqid(), -5);
        for ($i = 1; $i <= 11; $i++) {
            $this->review($tag . $i);
        }

        $this->actingAs($this->admin())
            ->get(route('admin.reviews.index', ['search' => $tag]))
            ->assertOk()
            ->assertSee('page=2', false); // 11 results, 10/page -> a second page exists
    }
}
