<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Common\ClockInterface;
use DateTimeImmutable;

class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
