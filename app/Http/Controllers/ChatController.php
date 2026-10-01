<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Fetch all conversations the user has participated in
        $conversations = Message::where('recipient_id', $userId)
            ->orWhere('sender_id', $userId)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) use ($userId) {
                return $item->sender_id === $userId ? $item->recipient_id : $item->sender_id;
            });

        return view('chat.index', compact('conversations'));
    }

    public function show($supplierId)
{
    $userId = Auth::id();

    // Fetch messages for the specific conversation
    $messages = Message::where(function ($query) use ($supplierId, $userId) {
        $query->where('sender_id', $userId)->where('recipient_id', $supplierId);
    })->orWhere(function ($query) use ($supplierId, $userId) {
        $query->where('sender_id', $supplierId)->where('recipient_id', $userId);
    })->with(['sender', 'recipient'])
    ->orderBy('created_at', 'asc')
    ->get();

    // Fetch recipient user details (supplier name)
    $recipient = User::findOrFail($supplierId);

    return view('chat.show', compact('messages', 'supplierId', 'recipient'));
}

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'content' => $request->message,
        ]);

        return redirect()->route('chat.show', $request->recipient_id);
    }
}
