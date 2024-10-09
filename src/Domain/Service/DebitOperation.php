<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Common\ClockInterface;
use App\Common\Result;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Value\Money;
use App\Domain\Model\Value\TransactionFee;

class DebitOperation
{
    public function __construct(
        private ClockInterface $clock,
        private TransactionFee $fee
    ) {
    }

    public function perform(BankAccount $account, Money $amount): Result
    {
        return $account->debit($amount, $this->clock->now(), $this->fee);
    }
}
