<?php

declare(strict_types=1);

namespace App\Common;

use Stringable;

/**
 * This clas should be from some extension or be a built-in type
 * We know how dangerous is storing money in float.
 */
class Decimal implements Stringable
{
    private const SCALE = 10000;

    private int $amount;

    public static function ofFloat(float $value): self
    {
        $amount = (int) round($value * self::SCALE);
        return new self($amount);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function one(): self
    {
        return new self(self::SCALE);
    }

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function add(Decimal $decimal): Decimal
    {
        return new self($this->amount + $decimal->amount);
    }

    public function subtract(Decimal $decimal): Decimal
    {
        return new self($this->amount - $decimal->amount);
    }

    public function multiply(Decimal $decimal): Decimal
    {
        return new self((int)($this->amount * $decimal->amount / self::SCALE));
    }

    public function gte(Decimal $other): bool
    {
        return $this->amount >= $other->amount;
    }

    public function equals(Decimal $other): bool
    {
        return $this->amount === $other->amount;
    }

    public function __toString(): string
    {
        return number_format($this->amount / self::SCALE, 4, '.', ' ');
    }
}
