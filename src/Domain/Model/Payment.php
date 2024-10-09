<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Value\Money;

class Payment
{
    // Identifier is required
    // Uuid $identifier;

    private Money $amount;

    public function __construct(Money $amount)
    {
        $this->amount = $amount;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }
}
