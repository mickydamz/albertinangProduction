<?php

namespace Tests\Feature;

use App\Mail\OrderCancelled;
use App\Mail\OrderCompleted;
use App\Mail\OrderDelivered;
use App\Mail\OrderProcessing;
use App\Mail\OrderReadyForPickup;
use App\Mail\OrderRefunded;
use App\Mail\OrderReviewRequest;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Order status-change emails (AdminOrderController@update).
 *
 * Every admin status change dispatches exactly one matching customer email
 * (and a review-request on completion). This locks that mapping down so a stray
 * edit to the match() block can't silently stop — or cross-wire — notifications.
 */
class OrderStatusEmailTest extends TestCase
{
    use DatabaseTransactions;

    /** Every status → the mailable it must trigger. */
    private const STATUS_MAIL = [
        'processing'       => OrderProcessing::class,
        'shipped'          => OrderShipped::class,
        'ready_for_pickup' => OrderReadyForPickup::class,
        'delivered'        => OrderDelivered::class,
        'completed'        => OrderCompleted::class,
        'cancelled'        => OrderCancelled::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function orderFor(User $customer, string $status = 'paid'): Order
    {
        return Order::create([
            'user_id'        => $customer->id,
            'status'         => $status,
            'total'          => 50000,
            'customer_email' => $customer->email,
        ]);
    }

    private function setStatus(User $admin, Order $order, string $status): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => $status,
        ]);
    }

    // ── The mapping ────────────────────────────────────────────────────────────

    /** @test */
    public function each_status_change_sends_exactly_its_matching_email()
    {
        foreach (self::STATUS_MAIL as $status => $mailable) {
            $customer = User::factory()->create();
            $previous = ['processing' => 'paid', 'shipped' => 'processing', 'ready_for_pickup' => 'processing', 'delivered' => 'shipped', 'completed' => 'delivered', 'cancelled' => 'paid'];
            $order = $this->orderFor($customer, $previous[$status]);
            $order->update(['fulfillment_method' => $status === 'ready_for_pickup' ? 'pickup' : 'delivery']);

            $this->setStatus($this->admin(), $order, $status)->assertRedirect();

            // These mailables implement ShouldQueue, so ->send() queues them.
            Mail::assertQueued($mailable, 1);
            Mail::assertQueued($mailable, fn ($mail) => $mail->hasTo($customer->email));

            // No OTHER status email should fire for this transition.
            foreach (self::STATUS_MAIL as $otherStatus => $otherMailable) {
                if ($otherMailable !== $mailable) {
                    Mail::assertNotQueued($otherMailable, function ($mail) use ($customer) {
                        return $mail->hasTo($customer->email);
                    });
                }
            }
        }
    }

    /** @test */
    public function completing_an_order_also_sends_a_review_request()
    {
        $customer = User::factory()->create();
        $order    = $this->orderFor($customer, 'delivered');

        $this->setStatus($this->admin(), $order, 'completed')->assertRedirect();

        Mail::assertQueued(OrderCompleted::class, fn ($m) => $m->hasTo($customer->email));
        Mail::assertQueued(OrderReviewRequest::class, fn ($m) => $m->hasTo($customer->email));
    }

    /** @test */
    public function a_review_request_is_only_sent_on_completion()
    {
        $customer = User::factory()->create();
        $order = $this->orderFor($customer, 'processing');
        $order->update(['fulfillment_method' => 'delivery']);

        $this->setStatus($this->admin(), $order, 'shipped')->assertRedirect();

        Mail::assertNotQueued(OrderReviewRequest::class);
    }

    /** @test */
    public function no_email_is_sent_when_the_status_does_not_change()
    {
        $customer = User::factory()->create();
        $order    = $this->orderFor($customer, 'processing');

        // Re-submitting the same status must not re-notify the customer.
        $this->setStatus($this->admin(), $order, 'processing')->assertRedirect();

        Mail::assertNothingQueued();
    }

    /** @test */
    public function the_status_email_goes_to_the_guest_customer_email_when_there_is_no_user()
    {
        // Guest order: no user_id, only a customer_email on the order.
        $order = Order::create([
            'user_id'        => null,
            'status'         => 'processing',
            'fulfillment_method' => 'delivery',
            'total'          => 50000,
            'customer_email' => 'guest@example.com',
        ]);

        $this->setStatus($this->admin(), $order, 'shipped')->assertRedirect();

        Mail::assertQueued(OrderShipped::class, fn ($m) => $m->hasTo('guest@example.com'));
    }
}
