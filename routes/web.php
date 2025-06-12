<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'chat');

Route::post('/chat', function (\Illuminate\Http\Request $request) {
    return response()->json(['reply' => $request->input('message')]);
});
