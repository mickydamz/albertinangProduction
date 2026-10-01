<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Mail\OrderDelivered;
use App\Mail\OrderProcessing;
use App\Mail\OrderReadyForPickup;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * When a customer chooses Pickup (Click & Collect), the emails must tell them
 * WHERE to collect: the specific pickup point (name + address), falling back to
 * the general location. They must never show a delivery address.
 *
 * Each Mailable is rendered to HTML and asserted on.
 */
class OrderEmailPickupTest extends TestCase
{
    use DatabaseTransactions;

    private const POINT_NAME = 'Uwani Collect Centre';
    private const POINT_ADDR = '17-18 Ziks Avenue, Uwani, Enugu';
    private const LOCATION   = 'Enugu';

    private function makePickupOrder(array $overrides = []): Order
    {
        $user    = User::factory()->create();
        $product = Product::create([
            'name' => 'Standing Fan ' . uniqid(), 'price' => 50000, 'stock' => 10, 'is_active' => true,
        ]);

        $order = Order::create(array_merge([
            'user_id'              => $user->id,
            'status'               => 'paid',
            'total'                => 50000,
            'total_usd'            => 0,
            'payment_method'       => 'paystack',
            'reference'            => 'ps_' . uniqid(),
            'order_number'         => 'ALB-' . rand(10000, 99999),
            'fulfillment_method'   => 'pickup',
            'pickup_location'      => self::LOCATION,
            'pickup_point_name'    => self::POINT_NAME,
            'pickup_point_address' => self::POINT_ADDR,
            'shipping_cost'        => 0,
            'customer_email'       => 'buyer@test.com',
        ], $overrides));

        $order->items()->create([
            'product_id' => $product->id, 'name' => $product->name, 'price' => 50000, 'quantity' => 1,
        ]);

        return $order;
    }

    /** Every lifecycle email that shows fulfilment details. */
    public function pickup_mailables(): array
    {
        return [
            'confirmation'    => [OrderConfirmation::class],
            'processing'      => [OrderProcessing::class],
            'shipped'         => [OrderShipped::class],
            'delivered'       => [OrderDelivered::class],
            'ready_for_pickup'=> [OrderReadyForPickup::class],
        ];
    }

    /**
     * @test
     * @dataProvider pickup_mailables
     */
    public function pickup_email_shows_the_pickup_point_name_and_address(string $mailable)
    {
        $order = $this->makePickupOrder();
        $html  = (new $mailable($order))->render();

        $this->assertStringContainsString(self::POINT_NAME, $html, "$mailable is missing the pickup point name");
        $this->assertStringContainsString(self::POINT_ADDR, $html, "$mailable is missing the pickup point address");
        $this->assertStringContainsString('Pickup', $html, "$mailable should label this as a pickup");
    }

    /**
     * @test
     * @dataProvider pickup_mailables
     */
    public function pickup_email_without_a_named_point_falls_back_to_the_location(string $mailable)
    {
        // Plain pickup (no specific point) — the general location must still show.
        $order = $this->makePickupOrder([
            'pickup_point_name'    => null,
            'pickup_point_address' => null,
        ]);
        $html = (new $mailable($order))->render();

        $this->assertStringContainsString(self::LOCATION, $html, "$mailable lost the pickup location fallback");
    }
}
