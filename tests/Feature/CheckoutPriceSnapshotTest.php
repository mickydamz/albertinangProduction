<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\User;
use App\Services\PaystackOrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Price integrity across a checkout: the price is snapshotted into PendingCheckout
 * at save-time, so a product price change AFTER checkout starts does not alter the
 * in-flight order. The customer is charged, and the order is created, at the price
 * they were shown — never a later price.
 */
class CheckoutPriceSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();
    }

    private function mockPaystack(string $reference, int $amountKobo): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status'  => true,
                'message' => 'Verification successful',
                'data'    => [
                    'id'        => 55554444,
                    'reference' => $reference,
                    'status'    => 'success',
                    'amount'    => $amountKobo,
                    'currency'  => 'NGN',
                    'customer'  => ['email' => 'buyer@test.com'],
                ],
            ], 200),
        ]);
    }

    /** @test */
    public function the_saved_checkout_snapshots_the_price_at_save_time()
    {
        $user    = User::factory()->create();
        $product = Product::create(['name' => 'Snapshot TV', 'price' => 50000, 'stock' => 10, 'is_active' => true]);

        $ref = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertOk()->json('reference');

        // Admin raises the price AFTER the snapshot was taken.
        $product->update(['price' => 80000]);

        $snapshot = PendingCheckout::where('reference', $ref)->firstOrFail();
        $this->assertSame(50000.0, (float) $snapshot->total_ngn);
        $this->assertSame(50000.0, (float) $snapshot->items[0]['effective_price_ngn']);
    }

    /** @test */
    public function an_order_is_created_at_the_snapshot_price_despite_a_later_price_change()
    {
        $user    = User::factory()->create();
        $product = Product::create(['name' => 'Snapshot Fridge', 'price' => 50000, 'stock' => 10, 'is_active' => true]);

        $ref = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertOk()->json('reference');

        // Price changes mid-flight; the customer already paid ₦50,000 for the snapshot.
        $product->update(['price' => 90000]);
        $this->mockPaystack($ref, 5000000); // ₦50,000 — matches the snapshot, not the new price

        $result = app(PaystackOrderService::class)->fulfil($ref, $user->id);
        $this->assertTrue($result['success'], $result['message']);

        $order = Order::where('reference', $ref)->firstOrFail();
        $this->assertSame(50000.0, (float) $order->total);
        $this->assertSame(50000.0, (float) $order->items()->first()->price);
    }
}
