<?php

declare(strict_types=1);

namespace App\Domain\Model\Value;

use App\Common\Decimal;
use InvalidArgumentException;
use Stringable;

class Money implements Stringable
{
    private Decimal $amount;
    private Currency $currency;

    public function __construct(Decimal $amount, Currency $currency)
    {
        $this->assertAmountIsPositive($amount);
        $this->amount = $amount;
        $this->currency = $currency;
    }

    private function assertAmountIsPositive(Decimal $amount): void
    {
        if (!$amount->gte(Decimal::zero())) {
            throw new InvalidArgumentException("Amount must be positive.");
        }
    }

    public function add(Money $money): Money
    {
        $this->assertSameCurrency($money);
        $newAmount = $this->amount->add($money->amount);
        return new Money($newAmount, $this->currency);
    }

    public function subtract(Money $money): Money
    {
        $this->assertSameCurrency($money);
        $newAmount = $this->amount->subtract($money->amount);
        return new Money($newAmount, $this->currency);
    }

    public function multiply(Decimal $factor): Money
    {
        return new Money(
            $this->amount->multiply($factor),
            $this->currency
        );
    }

    public function gte(Money $money): bool
    {
        $this->assertSameCurrency($money);
        return $this->amount->gte($money->amount);
    }


    public function equals(Money $other): bool
    {
        return $this->amount->equals($other->amount) && $this->currency->equals($other->currency);
    }

    private function assertSameCurrency(Money $money): void
    {
        if (!$this->currency->equals($money->currency)) {
            throw new InvalidArgumentException("Currency mismatch.");
        }
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function __toString(): string
    {
        return $this->amount->__toString() . ' ' . $this->currency->__toString();
    }
}
