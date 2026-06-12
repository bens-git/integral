<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('chat', function ($user) {
    return $user ? ['id' => $user->id, 'name' => $user->name] : null;
});

Broadcast::channel('online', function ($user) {
    return $user ? ['id' => $user->id, 'name' => $user->name] : null;
});