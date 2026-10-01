<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Mail\TicketReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────────────

    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->with(['replies.user'])
            ->latest()
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        return view('tickets.create');
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'     => 'required|max:255',
            'description' => 'required',
            'priority'    => 'required|in:low,medium,high',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('tickets', 'public');
            }
        }

        Ticket::create([
            'subject'     => $validated['subject'],
            'description' => $validated['description'],
            'priority'    => $validated['priority'],
            'user_id'     => auth()->id(),
            'images'      => $paths ?: null,
        ]);

        return redirect()->route('tickets.index')->with('status', 'Ticket created successfully!');
    }

    // ── Access guard ───────────────────────────────────────────────────────────

    /**
     * A ticket may only be viewed / edited / replied to by its owner or an admin.
     * Prevents users from reaching another customer's ticket by guessing the id.
     */
    private function authorizeAccess(Ticket $ticket): void
    {
        if ((int) $ticket->user_id !== (int) auth()->id() && auth()->user()?->role !== 'admin') {
            abort(403, 'This ticket does not belong to you.');
        }
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Ticket $ticket)
    {
        $this->authorizeAccess($ticket);

        $replies = $ticket->replies()->with('user')->get();

        return view('tickets.show', compact('ticket', 'replies'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Ticket $ticket)
    {
        $this->authorizeAccess($ticket);

        return view('tickets.edit', compact('ticket'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorizeAccess($ticket);

        $validated = $request->validate([
            'subject'     => 'required|max:255',
            'description' => 'required',
            'status'      => 'required|in:open,closed,pending',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.index')->with('status', 'Ticket updated successfully!');
    }

    // ── Reply ─────────────────────────────────────────────────────────────────

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorizeAccess($ticket);

        $validated = $request->validate([
            'message'  => 'required|string',
            'images'   => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('tickets', 'public');
            }
        }

        $newReply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $validated['message'],
            'images'    => $paths ?: null,
        ]);

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        // Notify the ticket owner when someone else replies (i.e. admin/support)
        if ((int) auth()->id() !== (int) $ticket->user_id) {
            $recipient = $ticket->user?->email;
            if ($recipient) {
                try {
                    Mail::to($recipient)->send(new TicketReplied($ticket, $newReply));
                } catch (\Exception $e) {
                    Log::error('Failed to send ticket reply email for ticket #' . $ticket->id . ': ' . $e->getMessage());
                }
            } else {
                Log::warning('No recipient email for ticket #' . $ticket->id . ' — reply notification not sent.');
            }
        }

        return redirect()->route('tickets.show', $ticket->id)->with('status', 'Reply added successfully!');
    }

    // ── Delete image ──────────────────────────────────────────────────────────

    public function deleteImage(Request $request, Ticket $ticket)
    {
        $this->authorizeAccess($ticket);

        $path   = $request->input('path');
        $images = $ticket->images ?? [];

        if (in_array($path, $images, true)) {
            Storage::disk('public')->delete($path);
            $ticket->images = array_values(array_filter($images, fn($i) => $i !== $path));
            $ticket->save();
        }

        return back()->with('success', 'Image removed.');
    }
}