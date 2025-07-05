<?php
declare(strict_types=1);

namespace App\Services;

readonly class EchoService implements ReplyInterface
{
    public function __construct(
        private string $message,
    )
    {
    }

    public function reply(): string
    {
        return $this->message;
    }
}
