<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\Channel;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'body'];

    protected $broadcastWith = ['id', 'user', 'body', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toArray($request = null)
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? ['id' => $this->user->id, 'name' => $this->user->name] : null,
            'body' => $this->body,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
