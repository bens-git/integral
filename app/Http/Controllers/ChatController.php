<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageCreated;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $messages = ChatMessage::with('user')->orderBy('created_at', 'desc')->limit(200)->get()->reverse()->values();

        return response()->json($messages->map(function ($m) {
            return [
                'id' => $m->id,
                'user' => ['id' => $m->user->id, 'name' => $m->user->name],
                'body' => $m->body,
                'created_at' => $m->created_at->toDateTimeString(),
            ];
        }));
    }

    public function store(Request $request)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $user = Auth::user();

        $msg = ChatMessage::create([
            'user_id' => $user->id,
            'body' => $request->input('body'),
        ]);

        $msg->load('user');
        broadcast(new ChatMessageCreated($msg));

        return response()->json([
            'id' => $msg->id,
            'user' => ['id' => $msg->user->id, 'name' => $msg->user->name],
            'body' => $msg->body,
            'created_at' => $msg->created_at->toDateTimeString(),
        ], 201);
    }

    // return online users within last 30 seconds
    public function online(Request $request)
    {
        $threshold = now()->subSeconds(30);
        $users = User::whereNotNull('last_seen_at')->where('last_seen_at', '>=', $threshold)->get(['id', 'name']);

        return response()->json($users);
    }

    // ping to mark user as online
    public function ping(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->last_seen_at = now();
            $user->save();
            broadcast(new UserOnline($user));
        }

        return response()->noContent();
    }
}
