<?php

namespace Tests\Feature;

use App\Mail\TicketReplied;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * User support tickets (TicketController, routes tickets.*).
 *
 * Covers: creating a ticket, the owner-scoped list, replying (which reopens a
 * closed ticket and notifies the owner when someone else replies), and the auth
 * guard. The reply notification (TicketReplied) is queued, so asserted as such.
 */
class SupportTicketTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();
    }

    private function makeTicket(User $owner, array $overrides = []): Ticket
    {
        return Ticket::create(array_merge([
            'subject'     => 'Order not delivered',
            'description' => 'My order has not arrived yet.',
            'priority'    => 'medium',
            'status'      => 'open',
            'user_id'     => $owner->id,
        ], $overrides));
    }

    // ── Create ─────────────────────────────────────────────────────────────────

    /** @test */
    public function a_user_can_open_the_create_ticket_page()
    {
        $this->actingAs(User::factory()->create())
            ->get('/tickets/create')
            ->assertOk();
    }

    /** @test */
    public function a_user_can_create_a_ticket()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tickets', [
            'subject'     => 'Damaged item',
            'description' => 'The blender arrived cracked.',
            'priority'    => 'high',
        ])->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('tickets', [
            'subject'  => 'Damaged item',
            'priority' => 'high',
            'user_id'  => $user->id,
        ]);
    }

    /** @test */
    public function creating_a_ticket_requires_subject_description_and_priority()
    {
        $this->actingAs(User::factory()->create())
            ->from('/tickets/create')
            ->post('/tickets', [])
            ->assertRedirect('/tickets/create')
            ->assertSessionHasErrors(['subject', 'description', 'priority']);
    }

    // ── List scoping ───────────────────────────────────────────────────────────

    /** @test */
    public function the_list_shows_only_the_users_own_tickets()
    {
        $me     = User::factory()->create();
        $other  = User::factory()->create();
        $mine   = $this->makeTicket($me);
        $theirs = $this->makeTicket($other);

        $res = $this->actingAs($me)->get('/tickets');

        $res->assertOk();
        $ids = $res->viewData('tickets')->pluck('id')->all();
        $this->assertContains($mine->id, $ids);
        $this->assertNotContains($theirs->id, $ids);
    }

    // ── Replies ────────────────────────────────────────────────────────────────

    /** @test */
    public function a_reply_from_support_reopens_a_closed_ticket_and_notifies_the_owner()
    {
        $owner   = User::factory()->create();
        $support = User::factory()->create(['role' => 'admin']);
        $ticket  = $this->makeTicket($owner, ['status' => 'closed']);

        $this->actingAs($support)->post("/tickets/{$ticket->id}/reply", [
            'message' => 'We are looking into this now.',
        ])->assertRedirect(route('tickets.show', $ticket->id));

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id'   => $support->id,
            'message'   => 'We are looking into this now.',
        ]);
        $this->assertSame('open', $ticket->fresh()->status, 'a reply reopens a closed ticket');
        Mail::assertQueued(TicketReplied::class, fn ($m) => $m->hasTo($owner->email));
    }

    /** @test */
    public function the_owner_replying_to_their_own_ticket_does_not_email_themselves()
    {
        $owner  = User::factory()->create();
        $ticket = $this->makeTicket($owner);

        $this->actingAs($owner)->post("/tickets/{$ticket->id}/reply", [
            'message' => 'Any update please?',
        ])->assertRedirect(route('tickets.show', $ticket->id));

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id'   => $owner->id,
        ]);
        Mail::assertNotQueued(TicketReplied::class);
    }

    // ── Ownership (IDOR guard) ──────────────────────────────────────────────────

    /** @test */
    public function the_owner_can_view_their_own_ticket()
    {
        $owner  = User::factory()->create();
        $ticket = $this->makeTicket($owner);

        $this->actingAs($owner)->get("/tickets/{$ticket->id}")->assertOk();
    }

    /** @test */
    public function a_user_cannot_view_another_users_ticket()
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();
        $ticket   = $this->makeTicket($owner);

        $this->actingAs($attacker)->get("/tickets/{$ticket->id}")->assertForbidden();
    }

    /** @test */
    public function a_user_cannot_reply_to_another_users_ticket()
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();
        $ticket   = $this->makeTicket($owner);

        $this->actingAs($attacker)->post("/tickets/{$ticket->id}/reply", [
            'message' => 'Sneaking into your ticket.',
        ])->assertForbidden();

        $this->assertDatabaseMissing('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id'   => $attacker->id,
        ]);
    }

    /** @test */
    public function an_admin_can_view_any_ticket()
    {
        $owner  = User::factory()->create();
        $admin  = User::factory()->create(['role' => 'admin']);
        $ticket = $this->makeTicket($owner);

        $this->actingAs($admin)->get("/tickets/{$ticket->id}")->assertOk();
    }

    // ── Guard ──────────────────────────────────────────────────────────────────

    /** @test */
    public function a_guest_cannot_reach_the_tickets_area()
    {
        $this->get('/tickets')->assertRedirect(route('login'));
        $this->get('/tickets/create')->assertRedirect(route('login'));
    }
}
