<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $secretKey;
    public $userName;
    public $message;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(string $secretKey, string $userName, string $message)
    {
        $this->secretKey = $secretKey;
        $this->userName = $userName;
        $this->message = $message;
        $this->timestamp = now()->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // We use a simple Channel (public) but with the secret key as part of the name.
        // This is "security by obscurity" regarding the channel name, but sufficient for this request.
        // A PrivateChannel would require an auth guard provider which we don't have (no users table).
        return [
            new Channel('chat.' . $this->secretKey),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
