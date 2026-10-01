<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Mail\OrderDelivered;
use App\Mail\OrderProcessing;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * The delivery emails must show the customer's actual shipping address (and the
 * resolved location/state), not a generic "your registered address" line — the
 * courier and the customer both rely on it. Pickup emails must instead show the
 * pickup location and never a delivery address.
 *
 * We render each Mailable to HTML and assert on its contents.
 */
class OrderEmailDeliveryAddressTest extends TestCase
{
    use DatabaseTransactions;

    private const ADDRESS = '7B Ada George Road, Rumuokwuta, Port Harcourt';

    private function makeOrder(array $overrides = []): Order
    {
        $user    = User::factory()->create();
        $product = Product::create([
            'name' => 'Standing Fan ' . uniqid(), 'price' => 50000, 'stock' => 10, 'is_active' => true,
        ]);

        $order = Order::create(array_merge([
            'user_id'                => $user->id,
            'status'                 => 'paid',
            'total'                  => 53500,
            'total_usd'              => 0,
            'payment_method'         => 'paystack',
            'reference'              => 'ps_' . uniqid(),
            'order_number'           => 'ALB-' . rand(10000, 99999),
            'fulfillment_method'     => 'delivery',
            'shipping_address'       => self::ADDRESS,
            'delivery_location_name' => 'Port Harcourt',
            'delivery_state_name'    => 'Rivers',
            'shipping_cost'          => 3500,
            'customer_email'         => 'buyer@test.com',
        ], $overrides));

        $order->items()->create([
            'product_id' => $product->id, 'name' => $product->name, 'price' => 50000, 'quantity' => 1,
        ]);

        return $order;
    }

    /** Delivery emails: the typed address + location/state must appear. */
    public function delivery_mailables(): array
    {
        return [
            'confirmation' => [OrderConfirmation::class],
            'processing'   => [OrderProcessing::class],
            'shipped'      => [OrderShipped::class],
            'delivered'    => [OrderDelivered::class],
        ];
    }

    /**
     * @test
     * @dataProvider delivery_mailables
     */
    public function delivery_email_includes_the_shipping_address(string $mailable)
    {
        $order = $this->makeOrder();
        $html  = (new $mailable($order))->render();

        $this->assertStringContainsString(self::ADDRESS, $html, "$mailable is missing the shipping address");
        $this->assertStringContainsString('Port Harcourt', $html, "$mailable is missing the delivery location");
        $this->assertStringContainsString('Rivers', $html, "$mailable is missing the delivery state");
    }

    /** @test */
    public function pickup_confirmation_shows_pickup_location_and_no_delivery_address()
    {
        $order = $this->makeOrder([
            'fulfillment_method'     => 'pickup',
            'pickup_location'        => 'Enugu HQ',
            'shipping_address'       => null,
            'delivery_location_name' => null,
            'delivery_state_name'    => null,
            'shipping_cost'          => 0,
        ]);

        $html = (new OrderConfirmation($order))->render();

        $this->assertStringContainsString('Enugu HQ', $html);
        $this->assertStringContainsString('Pickup Location', $html);
        $this->assertStringNotContainsString(self::ADDRESS, $html);
    }

    /** @test */
    public function delivery_email_falls_back_gracefully_when_address_is_missing()
    {
        // Older orders (pre-address) must not render an empty block or error.
        $order = $this->makeOrder(['shipping_address' => null]);
        $html  = (new OrderConfirmation($order))->render();

        $this->assertStringContainsString('registered address', $html);
    }
}
