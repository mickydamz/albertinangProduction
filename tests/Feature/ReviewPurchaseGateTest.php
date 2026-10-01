<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Storefront review gate (ReviewController@store, route reviews.store).
 *
 * The product page only shows the review form to buyers ("buy this product to
 * review"), and the server must enforce the same rule: a review can only be
 * created by a logged-in user who has actually purchased the product. These
 * tests assert that gate from every angle.
 */
class ReviewPurchaseGateTest extends TestCase
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

    /** Give $user an order for $product in the given status. */
    private function buy(User $user, Product $product, string $status = 'completed'): Order
    {
        $order = Order::create([
            'user_id' => $user->id,
            'status'  => $status,
            'total'   => 50000,
        ]);
        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => 50000,
            'quantity'   => 1,
        ]);
        return $order;
    }

    private function submitReview(Product $product, string $comment = 'This product is genuinely excellent.'): \Illuminate\Testing\TestResponse
    {
        return $this->postJson("/products/{$product->id}/reviews", [
            'product_id' => $product->id,
            'rating'     => 5,
            'comment'    => $comment,
        ]);
    }

    // ── The gate ────────────────────────────────────────────────────────────

    /** @test */
    public function a_guest_cannot_submit_a_review()
    {
        $product = $this->makeProduct();

        $this->submitReview($product)->assertStatus(401); // auth middleware

        $this->assertDatabaseMissing('reviews', ['product_id' => $product->id]);
    }

    /** @test */
    public function a_logged_in_user_who_did_not_buy_the_product_cannot_review_it()
    {
        $product = $this->makeProduct();
        $user    = User::factory()->create();

        $res = $this->actingAs($user)->submitReview($product);

        $res->assertStatus(403);
        $res->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('purchase', $res->json('message'));
        $this->assertDatabaseMissing('reviews', ['product_id' => $product->id, 'user_id' => $user->id]);
    }

    /** @test */
    public function an_order_in_a_non_fulfilled_status_does_not_unlock_reviewing()
    {
        $product = $this->makeProduct();
        $user    = User::factory()->create();
        $this->buy($user, $product, 'pending'); // not one of the qualifying statuses

        $res = $this->actingAs($user)->submitReview($product);

        $res->assertStatus(403);
        $this->assertDatabaseMissing('reviews', ['product_id' => $product->id, 'user_id' => $user->id]);
    }

    /** @test */
    public function a_user_who_bought_the_product_can_review_it()
    {
        $product = $this->makeProduct();
        $user    = User::factory()->create();
        $this->buy($user, $product, 'completed');

        $res = $this->actingAs($user)->submitReview($product, 'Bought it, love it — highly recommended.');

        $res->assertStatus(201);
        $res->assertJsonPath('success', true);
        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'content'    => 'Bought it, love it — highly recommended.',
            'rating'     => 5,
        ]);
    }

    /** @test */
    public function a_buyer_cannot_review_the_same_product_twice()
    {
        $product = $this->makeProduct();
        $user    = User::factory()->create();
        $this->buy($user, $product, 'delivered');

        // First review succeeds.
        $this->actingAs($user)->submitReview($product, 'First and only review here.')->assertStatus(201);

        // Second attempt is blocked.
        $res = $this->actingAs($user)->submitReview($product, 'Trying to review again now.');
        $res->assertStatus(422);
        $this->assertSame(1, Review::where('product_id', $product->id)->where('user_id', $user->id)->count());
    }

    /** @test */
    public function a_paid_order_unlocks_reviewing()
    {
        // Regression guard: Paystack orders are created with status 'paid'
        // (PaystackOrderService). The product page showed the review form for a
        // paid order, but the store gate previously omitted 'paid' and rejected
        // the submission — form shown, submit refused. The two lists must agree.
        $product = $this->makeProduct();
        $user    = User::factory()->create();
        $this->buy($user, $product, 'paid');

        $res = $this->actingAs($user)->submitReview($product, 'Paid via Paystack — works great.');

        $res->assertStatus(201);
        $res->assertJsonPath('success', true);
        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'rating'     => 5,
        ]);
    }

    /** @test */
    public function every_qualifying_order_status_unlocks_reviewing()
    {
        // Locks the full accepted-status list so the ProductController (form
        // display) and ReviewController (submit) can never drift apart again.
        foreach (['paid', 'processing', 'shipped', 'completed', 'delivered'] as $status) {
            $product = $this->makeProduct();
            $user    = User::factory()->create();
            $this->buy($user, $product, $status);

            $res = $this->actingAs($user)->submitReview($product, "Ordered and received ({$status}).");

            $this->assertSame(201, $res->status(), "Status '{$status}' should unlock reviewing");
            $this->assertDatabaseHas('reviews', [
                'product_id' => $product->id,
                'user_id'    => $user->id,
            ]);
        }
    }
}
