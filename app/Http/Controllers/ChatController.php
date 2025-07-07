<?php

namespace App\Http\Controllers;

use App\Assistants\Assistant;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): RedirectResponse
    {
        $chat = auth()->user()->chats()->latest()->first();

        return redirect()->route('chat.show', $chat);
    }

    public function show(Chat $chat): View
    {
        $this->authorize('view', $chat);
        $data = [
            'chat' => $chat,
            'chats' => auth()->user()->chats()->latest()->get(),
        ];

        return view('chat', $data);
    }

    public function store(Request $request): RedirectResponse
    {
        $chat = auth()->user()->chats()->create();
        $chat->title = "Чат #$chat->id";
        $chat->save();

        return redirect()->route('chat.show', $chat);
    }

    public function messages(Chat $chat): JsonResponse
    {
        $this->authorize('view', $chat);

        $messages = $chat->messages()->oldest()->get();

        return response()->json(['messages' => $messages]);
    }

    public function storeMessage(Request $request, Chat $chat): JsonResponse
    {
        $this->authorize('view', $chat);

        $userMessage = $chat->messages()->create([
            'sender'  => 'user',
            'content' => $request->get('message'),
        ]);

        // Получаем ответ от ассистента
        $assistant    = Assistant::make($request->get('mode'));
        $responseText = $assistant->reply($request->get('message'));

        $assistantMessage = $chat->messages()->create([
            'sender'  => 'assistant',
            'content' => $responseText,
        ]);

        if (!$chat->title) {
            $chat->update(['title' => substr($request->get('message'), 0, 50)]);
        }

        return response()->json([
            'user_message'      => $userMessage,
            'assistant_message' => $assistantMessage,
        ]);
    }
}
