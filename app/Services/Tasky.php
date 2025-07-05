<?php
declare(strict_types=1);

namespace App\Services;

readonly class Tasky implements ReplyInterface
{
    public function __construct(
        private string $message,
    )
    {
    }

    public function reply(): string
    {
        return 'Функционал находится в разработке...';
    }
}
