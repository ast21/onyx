<?php
declare(strict_types=1);

namespace App\Enums;

use App\Services\EchoService;
use App\Services\ReplyInterface;
use App\Services\Tasky;

enum Mode: string
{
    case Echo  = 'echo';
    case Tasky = 'tasky';

    public function getService(string $message): ReplyInterface
    {
        return match ($this) {
            self::Echo  => new EchoService($message),
            self::Tasky => new Tasky($message),
        };
    }
}
