<?php

declare(strict_types=1);

namespace App\Domain\Model\Value\Limit;

use App\Domain\Model\Value\Money;
use DateTimeImmutable;

class DailyOperationCountLimitPolicy implements LimitPolicy
{
    private int $dailyLimit;

    private ?DateTimeImmutable $date = null;
    private int $count = 0;

    public function __construct(int $dailyLimit)
    {
        $this->dailyLimit = $dailyLimit;
    }

    public function perform(Money $money, DateTimeImmutable $dateTime): bool
    {
        if (!$this->date) {
            $this->date = $dateTime;
        }

        if ($this->date->format('Y-m-d') !== $dateTime->format('Y-m-d')) {
            $this->date = $dateTime;
            $this->count = 0;
        }

        if ($this->count < $this->dailyLimit) {
            $this->count++;
            return true;
        }

        return false;
    }
}
