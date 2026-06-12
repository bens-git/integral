<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Inertia\Inertia;
use App\Http\Controllers\ChatController;

Route::middleware('web')->group(function () {
    Route::get('/', function () {
        return Inertia::render('LandingPage');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
        Route::get('/messages', [App\Http\Controllers\ChatController::class, 'index'])->name('messages.index');
        Route::post('/messages', [App\Http\Controllers\ChatController::class, 'store'])->name('messages.store');
        Route::get('/online', [App\Http\Controllers\ChatController::class, 'online'])->name('online');
        Route::post('/ping', [App\Http\Controllers\ChatController::class, 'ping'])->name('ping');
    });

    Route::get('/demo-login', [App\Http\Controllers\DemoAuthController::class, 'show'])->name('demo.login');
    Route::post('/demo-login', [App\Http\Controllers\DemoAuthController::class, 'login'])->name('demo.login.submit');
    Route::get('/login', [App\Http\Controllers\DemoAuthController::class, 'show'])->name('login');
    Route::post('/login', [App\Http\Controllers\DemoAuthController::class, 'login']);

    Broadcast::routes(['middleware' => ['auth', 'verified']]);

    require __DIR__ . '/channels.php';
    require __DIR__ . '/auth.php';
    require __DIR__ . '/cds.php';
});
