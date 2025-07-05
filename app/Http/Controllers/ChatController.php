<?php

namespace App\Http\Controllers;

use App\Enums\Mode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    public function index(): View
    {
        return view('chat');
    }

    public function reply(Request $request): JsonResponse
    {
        $service = Mode::from($request->get('mode'))
            ->getService($request->get('message'));

        $data = [
            'reply' => $service->reply(),
        ];

        return response()
            ->json($data)
            ->setStatusCode(Response::HTTP_OK);
    }
}
