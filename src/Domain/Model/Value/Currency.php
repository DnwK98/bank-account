<?php

declare(strict_types=1);

namespace App\Domain\Model\Value;

use Stringable;

class Currency implements Stringable
{
    private string $code;

    public function __construct(string $code)
    {
        $this->assertCurrencyCodeIsValid($code);
        $this->code = strtoupper($code);
    }

    public function equals(Currency $currency): bool
    {
        return $this->code === $currency->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }

    private function assertCurrencyCodeIsValid(string $code): void
    {
        if (strlen($code) !== 3) {
            throw new \InvalidArgumentException("Invalid currency code: $code");
        }
    }
}
