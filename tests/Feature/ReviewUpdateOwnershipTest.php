<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Review editing authorization (ReviewController@update, route reviews.update
 * PUT /reviews/{reviewId}).
 *
 * The controller allows an edit only for the review's owner or an admin. This
 * is an object-level authorization (IDOR) guard, so it is pinned down here from
 * every angle — a refactor that drops the check would otherwise pass silently.
 */
class ReviewUpdateOwnershipTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeProduct(): Product
    {
        return Product::create([
            'name'      => 'Reviewable Product ' . uniqid(),
            'price'     => 50000,
            'stock'     => 10,
            'is_active' => true,
        ]);
    }

    private function makeReview(User $user, Product $product, string $content = 'Original review content.'): Review
    {
        return Review::create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'rating'     => 4,
            'content'    => $content,
        ]);
    }

    private function edit(User $actor, int $reviewId, array $payload = []): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($actor)->putJson("/reviews/{$reviewId}", array_merge([
            'comment' => 'Edited review content that is long enough.',
            'rating'  => 5,
        ], $payload));
    }

    // ── The gate ────────────────────────────────────────────────────────────

    /** @test */
    public function the_owner_can_edit_their_own_review()
    {
        $product = $this->makeProduct();
        $owner   = User::factory()->create();
        $review  = $this->makeReview($owner, $product);

        $res = $this->edit($owner, $review->id, ['comment' => 'Updated after using it longer.', 'rating' => 5]);

        $res->assertStatus(200)->assertJsonPath('success', true);
        $this->assertDatabaseHas('reviews', [
            'id'      => $review->id,
            'content' => 'Updated after using it longer.',
            'rating'  => 5,
        ]);
    }

    /** @test */
    public function a_non_owner_cannot_edit_someone_elses_review()
    {
        $product   = $this->makeProduct();
        $owner     = User::factory()->create();
        $attacker  = User::factory()->create();
        $review    = $this->makeReview($owner, $product, 'The real owner wrote this.');

        $res = $this->edit($attacker, $review->id, ['comment' => 'Hijacked content.', 'rating' => 1]);

        $res->assertStatus(403)->assertJsonPath('success', false);
        // The review must be completely untouched.
        $this->assertDatabaseHas('reviews', [
            'id'      => $review->id,
            'content' => 'The real owner wrote this.',
            'rating'  => 4,
        ]);
    }

    /** @test */
    public function an_admin_can_edit_any_review()
    {
        $product = $this->makeProduct();
        $owner   = User::factory()->create();
        $admin   = User::factory()->create(['role' => 'admin']);
        $review  = $this->makeReview($owner, $product);

        $res = $this->edit($admin, $review->id, ['comment' => 'Moderated by an administrator.', 'rating' => 3]);

        $res->assertStatus(200)->assertJsonPath('success', true);
        $this->assertDatabaseHas('reviews', [
            'id'      => $review->id,
            'content' => 'Moderated by an administrator.',
            'rating'  => 3,
        ]);
    }

    /** @test */
    public function a_guest_cannot_edit_a_review()
    {
        $product = $this->makeProduct();
        $owner   = User::factory()->create();
        $review  = $this->makeReview($owner, $product, 'Owner content stays put.');

        $res = $this->putJson("/reviews/{$review->id}", [
            'comment' => 'Guest tampering attempt.',
            'rating'  => 1,
        ]);

        $res->assertStatus(401); // auth middleware
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'content' => 'Owner content stays put.']);
    }

    /** @test */
    public function editing_a_missing_review_returns_404()
    {
        $user = User::factory()->create();

        $res = $this->edit($user, 999999);

        $res->assertStatus(404)->assertJsonPath('success', false);
    }
}
