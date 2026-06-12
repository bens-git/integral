<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
        $this->message->load('user');
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'user' => ['id' => $this->message->user->id, 'name' => $this->message->user->name],
            'body' => $this->message->body,
            'created_at' => $this->message->created_at?->toDateTimeString(),
        ];
    }
}