<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        return view('chat');
    }

    public function send(Request $request): JsonResponse
    {
        usleep(500 * 1000);

        return response()->json([
            'reply' => $request->input('message'),
        ]);
    }
}
