<?php

declare(strict_types=1);

namespace App\Domain\Model\Value;

use App\Common\Decimal;

class TransactionFee
{
    private Decimal $fee;

    public static function zero(): self
    {
        return new self(Decimal::zero());
    }

    public static function percent(float $percent): self
    {
        return new self(Decimal::ofFloat($percent / 100));
    }

    public function __construct(Decimal $fee)
    {
        if (!$fee->gte(Decimal::zero())) {
            throw new \InvalidArgumentException('Fee should be greater than 0');
        }

        $this->fee = $fee;
    }

    public function apply(Money $money): Money
    {
        return $money->multiply(Decimal::one()->add($this->fee));
    }
}
