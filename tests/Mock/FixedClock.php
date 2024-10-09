<?php

declare(strict_types=1);

namespace App\Tests\Mock;

use App\Common\ClockInterface;
use DateTimeImmutable;

class FixedClock implements ClockInterface
{
    public function __construct(
        private DateTimeImmutable $time = new DateTimeImmutable(),
    ) {
    }

    public function set(DateTimeImmutable $now): void
    {
        $this->time = $now;
    }

    public function now(): DateTimeImmutable
    {
        return $this->time;
    }
}