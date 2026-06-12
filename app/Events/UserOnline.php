<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnline implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('online');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
        ];
    }
}