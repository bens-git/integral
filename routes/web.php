<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/', function () {
    return Inertia::render('LandingPage');
});

// Chat endpoints (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/messages', [ChatController::class, 'index'])->name('chat.messages.index');
    Route::post('/chat/messages', [ChatController::class, 'store'])->name('chat.messages.store');
    Route::get('/chat/online', [ChatController::class, 'online'])->name('chat.online');
    Route::post('/chat/ping', [ChatController::class, 'ping'])->name('chat.ping');
});

// Demo login (pick a nickname to sign in instantly)
Route::get('/demo-login', [App\Http\Controllers\DemoAuthController::class, 'show'])->name('demo.login');
Route::post('/demo-login', [App\Http\Controllers\DemoAuthController::class, 'login'])->name('demo.login.submit');

// Provide a lightweight 'login' route name so other parts of the app (and packages) that expect
// a 'login' named route continue to work — forward to demo login.
Route::get('/login', [App\Http\Controllers\DemoAuthController::class, 'show'])->name('login');
Route::post('/login', [App\Http\Controllers\DemoAuthController::class, 'login']);

// Application dashboard (uses DB-driven stats)
Route::middleware('auth')->get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/auth.php';
require __DIR__.'/cds.php';