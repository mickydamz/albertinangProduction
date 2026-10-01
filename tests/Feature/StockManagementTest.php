<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\User;
use App\Services\PaystackOrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Stock lifecycle: deducted when a paid order is created (oversell allowed and
 * flagged, never rejecting a paid order) and restored exactly once when the
 * order is cancelled/refunded — re-deducted if that release is reversed.
 */
class StockManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function makeProduct(int $stock): Product
    {
        return Product::create([
            'name'      => 'Stocked Item ' . uniqid(),
            'price'     => 10000,
            'stock'     => $stock,
            'is_active' => true,
        ]);
    }

    /** A paid order carrying one line item for $product × $qty, with stock already deducted. */
    private function paidOrderFor(Product $product, int $qty): Order
    {
        $order = Order::create([
            'user_id'        => User::factory()->create()->id,
            'status'         => 'paid',
            'total'          => 10000 * $qty,
            'total_usd'      => 0,
            'payment_method' => 'paystack',
            'reference'      => 'ps_' . uniqid(),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => 10000,
            'quantity'   => $qty,
        ]);
        $order->deductStock();

        return $order;
    }

    // ── Deduction at fulfilment ─────────────────────────────────────────────────

    /** @test */
    public function fulfilling_a_paystack_order_deducts_stock()
    {
        $product = $this->makeProduct(10);
        $ref     = 'ps_stock_' . uniqid();

        PendingCheckout::create([
            'reference'      => $ref,
            'user_id'        => null,
            'customer_email' => 'buyer@test.com',
            'total_ngn'      => 30000,
            'items'          => [[
                'id' => $product->id, 'name' => $product->name,
                'basePriceNgn' => 10000, 'effective_price_ngn' => 10000, 'quantity' => 3,
                'image' => null, 'sku' => null,
                'installation_option' => null, 'installation_extra_ngn' => 0,
            ]],
            'fulfillment' => ['method' => 'pickup', 'pickup_location' => 'Enugu HQ'],
            'coupon'      => null,
        ]);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data'   => [
                    'id' => 1, 'reference' => $ref, 'status' => 'success',
                    'amount' => 3000000, 'currency' => 'NGN', 'customer' => ['email' => 'buyer@test.com'],
                ],
            ], 200),
        ]);

        app(PaystackOrderService::class)->fulfil($ref, null);

        $this->assertSame(7, (int) $product->fresh()->stock);   // 10 - 3
    }

    /** @test */
    public function deduction_allows_oversell_into_negative_stock()
    {
        $product = $this->makeProduct(1);

        $this->paidOrderFor($product, 2);   // bought 2 of the last 1

        $this->assertSame(-1, (int) $product->fresh()->stock);  // flagged oversold, order still created
    }

    // ── Restoration on release ──────────────────────────────────────────────────

    /** @test */
    public function cancelling_an_order_restores_its_stock()
    {
        $product = $this->makeProduct(10);
        $order   = $this->paidOrderFor($product, 3);
        $this->assertSame(7, (int) $product->fresh()->stock);

        $order->update(['status' => 'cancelled']);

        $this->assertSame(10, (int) $product->fresh()->stock);
        $this->assertNotNull($order->fresh()->stock_restored_at);
    }

    /** @test */
    public function refunding_an_order_restores_its_stock()
    {
        $product = $this->makeProduct(5);
        $order   = $this->paidOrderFor($product, 2);

        $order->update(['status' => 'refunded']);

        $this->assertSame(5, (int) $product->fresh()->stock);
    }

    /** @test */
    public function stock_is_restored_only_once_across_releasing_transitions()
    {
        $product = $this->makeProduct(10);
        $order   = $this->paidOrderFor($product, 4);   // stock 6

        $order->update(['status' => 'refund_pending']); // restore -> 10
        $order->update(['status' => 'refunded']);       // already restored -> no-op

        $this->assertSame(10, (int) $product->fresh()->stock);
    }

    /** @test */
    public function rejecting_a_cancellation_re_deducts_stock()
    {
        $product = $this->makeProduct(10);
        $order   = $this->paidOrderFor($product, 3);    // stock 7

        $order->update(['status' => 'cancelled']);       // restore -> 10
        $this->assertSame(10, (int) $product->fresh()->stock);

        $order->update(['status' => 'paid']);            // reversal -> re-deduct -> 7

        $this->assertSame(7, (int) $product->fresh()->stock);
        $this->assertNull($order->fresh()->stock_restored_at);
    }
}
