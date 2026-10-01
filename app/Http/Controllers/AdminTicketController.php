<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Mail\TicketReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminTicketController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Ticket::with('user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', "%$s%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$s%")
                                                     ->orWhere('email', 'like', "%$s%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(15)->withQueryString();
        return view('admin.tickets.index', compact('tickets'));
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Ticket $ticket)
    {
        $replies = $ticket->replies()->with('user')->get();
        return view('admin.tickets.show', compact('ticket', 'replies'));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        $users = User::all();
        return view('admin.tickets.create', compact('users'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|max:255',
            'description' => 'required',
            'status'      => 'required|in:open,pending,closed',
            'priority'    => 'required|in:low,medium,high',
            'user_id'     => 'required|exists:users,id',
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
            'subject'     => $request->subject,
            'description' => $request->description,
            'priority'    => $request->priority,
            'status'      => $request->status,
            'user_id'     => $request->user_id,
            'images'      => $paths ?: null,
        ]);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket created successfully!');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Ticket $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    // ── Update ticket fields ──────────────────────────────────────────────────

    public function updateTicket(Request $request, Ticket $ticket)
    {
        $request->validate([
            'subject'     => 'required|max:255',
            'description' => 'required',
            'status'      => 'required|in:open,pending,closed',
            'priority'    => 'required|in:low,medium,high',
        ]);

        $ticket->update([
            'subject'     => $request->subject,
            'description' => $request->description,
            'status'      => $request->status,
            'priority'    => $request->priority,
        ]);

        return redirect()->route('admin.tickets.index')->with('status', 'Ticket updated successfully!');
    }

    // ── Update status only (from show page) ───────────────────────────────────

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,pending,closed',
        ]);

        $ticket->update(['status' => $request->status]);

        return redirect()->route('admin.tickets.index')->with('status', 'Ticket status updated successfully!');
    }

    // ── Reply ─────────────────────────────────────────────────────────────────

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
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
            'message'   => $request->message,
            'images'    => $paths ?: null,
        ]);

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        // Notify the ticket owner of the admin reply
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

        return redirect()->route('admin.tickets.show', $ticket->id)->with('status', 'Reply added successfully!');
    }
}