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
    Route::redirect('/', 'chat');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat', [ChatController::class, 'send']);

    Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');
});
