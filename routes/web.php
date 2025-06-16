<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'chat');

Route::prefix('/chat')->group(function () {
    Route::view('/', 'chat');
    Route::post('/', function (\Illuminate\Http\Request $request) {
        sleep(1);
        return response()->json(['reply' => $request->input('message')]);
    });
});
