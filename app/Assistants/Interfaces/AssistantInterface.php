<?php
declare(strict_types=1);

namespace App\Assistants\Interfaces;

interface AssistantInterface
{
    public function reply(string $message): string;
}
