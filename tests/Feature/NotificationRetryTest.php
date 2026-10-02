<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderNotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * OrderNotificationService: delivery is idempotent and retryable.
 *
 * A send is recorded once (unique event_key), a failed delivery is parked with its
 * error for retry, and a retry delivers the email WITHOUT creating a second
 * notification row or a second order. This is what lets the reconcile command
 * re-send failed emails safely.
 */
class NotificationRetryTest extends TestCase
{
    use DatabaseTransactions;

    private function makeOrder(): Order
    {
        $user = User::factory()->create(['email' => 'buyer_' . uniqid() . '@test.com']);

        return Order::create([
            'user_id'        => $user->id,
            'status'         => 'paid',
            'total'          => 50000,
            'payment_method' => 'paystack',
            'customer_email' => $user->email,
        ]);
    }

    /** @test */
    public function a_repeated_send_does_not_create_a_second_notification_or_email()
    {
        Mail::fake();
        $order = $this->makeOrder();
        $service = app(OrderNotificationService::class);

        $service->send($order, OrderConfirmation::class, 'confirmation');
        $service->send($order, OrderConfirmation::class, 'confirmation'); // duplicate event

        Mail::assertSent(OrderConfirmation::class, 1);
        $this->assertSame(1, DB::table('order_notifications')->where('order_id', $order->id)->count());
    }

    /** @test */
    public function a_failed_delivery_is_parked_with_its_error_and_can_be_retried()
    {
        $order = $this->makeOrder();
        $service = app(OrderNotificationService::class);

        // First attempt: the mailer is down — the error is swallowed and recorded.
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        $service->send($order, OrderConfirmation::class, 'confirmation');

        $row = DB::table('order_notifications')->where('order_id', $order->id)->first();
        $this->assertNotNull($row);
        $this->assertNull($row->sent_at);
        $this->assertSame(1, (int) $row->attempts);
        $this->assertNotNull($row->last_error);

        // Retry once the mailer is back: it delivers, clears the error, and never
        // duplicates the notification row or the order.
        Mail::fake();
        $service->deliver($row->id);

        Mail::assertSent(OrderConfirmation::class, 1);
        $fresh = DB::table('order_notifications')->where('id', $row->id)->first();
        $this->assertNotNull($fresh->sent_at);
        $this->assertNull($fresh->last_error);
        $this->assertSame(1, DB::table('order_notifications')->where('order_id', $order->id)->count());
        $this->assertSame(1, Order::where('id', $order->id)->count());
    }

    /** @test */
    public function delivering_an_already_sent_notification_is_a_no_op()
    {
        Mail::fake();
        $order = $this->makeOrder();
        $service = app(OrderNotificationService::class);

        $service->send($order, OrderConfirmation::class, 'confirmation');
        $id = DB::table('order_notifications')->where('order_id', $order->id)->value('id');

        $service->deliver($id); // second delivery of the same, already-sent row

        Mail::assertSent(OrderConfirmation::class, 1);
    }
}
