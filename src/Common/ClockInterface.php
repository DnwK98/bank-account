<?php

declare(strict_types=1);

namespace App\Common;

use DateTimeImmutable;

interface ClockInterface
{
    public function now(): DateTimeImmutable;
}
