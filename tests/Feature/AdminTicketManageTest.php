<?php

namespace Tests\Feature;

use App\Mail\TicketReplied;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Admin support-ticket management (AdminTicketController).
 * Admins can reply to any ticket (which reopens a closed one and notifies the
 * owner) and change a ticket's status.
 */
class AdminTicketManageTest extends TestCase
{
    use DatabaseTransactions;

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

    private function ticket(User $owner, array $overrides = []): Ticket
    {
        return Ticket::create(array_merge([
            'subject'     => 'Help needed',
            'description' => 'Something went wrong.',
            'priority'    => 'medium',
            'status'      => 'open',
            'user_id'     => $owner->id,
        ], $overrides));
    }

    /** @test */
    public function an_admin_reply_reopens_a_closed_ticket_and_emails_the_owner()
    {
        $owner  = User::factory()->create();
        $ticket = $this->ticket($owner, ['status' => 'closed']);

        $this->actingAs($this->admin())->post("/admin/tickets/{$ticket->id}/reply", [
            'message' => 'We have resolved this for you.',
        ])->assertRedirect(route('admin.tickets.show', $ticket->id));

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'message'   => 'We have resolved this for you.',
        ]);
        $this->assertSame('open', $ticket->fresh()->status);
        Mail::assertQueued(TicketReplied::class, fn ($m) => $m->hasTo($owner->email));
    }

    /** @test */
    public function an_admin_can_change_a_ticket_status()
    {
        $ticket = $this->ticket(User::factory()->create());

        $this->actingAs($this->admin())->put("/admin/tickets/{$ticket->id}", [
            'status' => 'pending',
        ])->assertRedirect(route('admin.tickets.index'));

        $this->assertSame('pending', $ticket->fresh()->status);
    }

    /** @test */
    public function a_ticket_status_must_be_a_valid_value()
    {
        $ticket = $this->ticket(User::factory()->create());

        $this->actingAs($this->admin())->from(route('admin.tickets.index'))
            ->put("/admin/tickets/{$ticket->id}", ['status' => 'banana'])
            ->assertSessionHasErrors('status');
    }
}
