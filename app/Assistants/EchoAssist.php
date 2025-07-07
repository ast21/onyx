<?php
declare(strict_types=1);

namespace App\Assistants;

use App\Assistants\Interfaces\AssistantInterface;

readonly class EchoAssist implements AssistantInterface
{
    public function reply(string $message): string
    {
        return $message;
    }
}
