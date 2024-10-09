<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Common\Result;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Value\Money;

class CreditOperation
{
    public function perform(BankAccount $account, Money $amount): Result
    {
        return $account->credit($amount);
    }
}
