<?php

declare(strict_types=1);

namespace App\Domain\Model\Value\Limit;

use App\Domain\Model\Value\Money;
use DateTimeImmutable;

interface LimitPolicy
{
    public function perform(Money $money, DateTimeImmutable $dateTime): bool;
}
