<?php
declare(strict_types=1);

namespace App\Assistants;

use App\Assistants\Interfaces\AssistantInterface;

readonly class TaskyAssist implements AssistantInterface
{
    public function reply(string $message): string
    {
        return 'Функционал находится в разработке...';
    }
}
