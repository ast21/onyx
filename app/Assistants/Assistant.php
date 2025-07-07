<?php
declare(strict_types=1);

namespace App\Assistants;

use App\Assistants\Interfaces\AssistantInterface;

class Assistant
{
    private const MAPPING = [
        'echo'  => EchoAssist::class,
        'tasky' => TaskyAssist::class,
    ];

    public static function make(string $assistant): AssistantInterface
    {
        $assistant = self::MAPPING[$assistant] ?? null;

        if (!$assistant) {
            abort(404, 'Assistant not found');
        }

        return new $assistant;
    }
}
