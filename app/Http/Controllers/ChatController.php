<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Create a new chat room and redirect the user to it.
     */
    public function create()
    {
        // Generate a secure random hex key (32 bytes = 64 hex characters)
        $secretKey = bin2hex(random_bytes(32));

        return redirect()->route('chat.join', ['secretKey' => $secretKey]);
    }

    /**
     * Join a chat room.
     */
    public function join(string $secretKey)
    {
        // Simple session-based auth check
        if (! session()->has('chat_user_name')) {
            return redirect()->route('chat.login', ['secretKey' => $secretKey]);
        }

        return view('chat.room', [
            'secretKey' => $secretKey,
            'userName' => session('chat_user_name'),
        ]);
    }

    /**
     * Show the login form for a specific chat.
     */
    public function showLogin(string $secretKey)
    {
        return view('chat.login', ['secretKey' => $secretKey]);
    }

    /**
     * Handle the login (store name in session).
     */
    public function login(Request $request, string $secretKey)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        session(['chat_user_name' => $validated['name']]);

        return redirect()->route('chat.join', ['secretKey' => $secretKey]);
    }

    /**
     * Send a message to the chat room.
     */
    public function sendMessage(Request $request, string $secretKey)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userName = session('chat_user_name', 'Anonymous');

        // Broadcast the message
        // using broadcast() helper or Event::dispatch
        broadcast(new MessageSent($secretKey, $userName, $validated['message']));

        return response()->json(['status' => 'Message Sent!']);
    }
    
    /**
     * Logout from the session (optional, but good for "leaving").
     */
    public function leave()
    {
        session()->forget('chat_user_name');
        return redirect('/');
    }
}
