<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');

    Route::get('auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::redirect('/', 'chats');

    Route::get('/chats', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chats', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chats/{chat}/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chats/messages', [ChatController::class, 'storeChatAndMessage']);
    Route::post('/chats/{chat}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');

    Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');
});
