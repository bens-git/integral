<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DemoAuthController extends Controller
{
    /**
     * Show the demo login form (Inertia page)
     */
    public function show()
    {
        return Inertia::render('DemoLogin');
    }

    /**
     * Accept a nickname and log the corresponding demo user in, creating them if needed.
     */
    public function login(Request $request)
    {
        $request->validate([
            'nickname' => ['required', 'string', 'max:48'],
        ]);

        $nickname = trim($request->input('nickname')) ?: 'DemoUser';
        $slug = Str::slug($nickname, '.');
        $email = "demo+{$slug}@example.test";

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $nickname,
                'email' => $email,
                // give a random password; auth will not require it
                'password' => Hash::make(Str::random(32)),
            ]);

            // ensure email is marked verified even if not fillable
            $user->email_verified_at = now();
            $user->save();
        } else {
            // if an existing demo user somehow wasn't verified, verify now
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
            }
        }

        Auth::login($user);

        return redirect()->intended('/');
    }
}
