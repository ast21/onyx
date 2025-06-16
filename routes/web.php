<?php

use App\Http\Controllers\Auth\GoogleController;
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

    Route::view('/chat', 'chat')->name('chat');
    Route::post('/chat', function (\Illuminate\Http\Request $request) {
        sleep(1);
        return response()->json(['reply' => $request->input('message')]);
    });

    Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');
});
