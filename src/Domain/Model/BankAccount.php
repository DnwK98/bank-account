<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Common\Result;
use App\Domain\Model\Value\Currency;
use App\Domain\Model\Value\Limit\LimitPolicy;
use App\Domain\Model\Value\Money;
use App\Domain\Model\Value\TransactionFee;
use DateTimeImmutable;

class BankAccount
{
    // Identifier is required for entities
    // BankAccountIdentifier $identifier;

    private LimitPolicy $debitLimitPolicy;
    private Money $balance;

    // Optimistic locking is required for this aggregate
    // int version;

    public function __construct(LimitPolicy $limit, Money $initialBalance)
    {
        $this->debitLimitPolicy = $limit;
        $this->balance = $initialBalance;
    }

    public function credit(Money $money): Result
    {
        if (!$this->getCurrency()->equals($money->getCurrency())) {
            return Result::failure("Account currency is different than given one.");
        }

        $this->balance = $this->balance->add($money);

        return Result::success();
    }

    public function debit(Money $money, DateTimeImmutable $dateTime, TransactionFee $fee): Result
    {
        $totalDebit = $fee->apply($money);

        if (!$this->getCurrency()->equals($totalDebit->getCurrency())) {
            return Result::failure("Account currency is different than given one.");
        }

        if (!$this->balance->gte($totalDebit)) {
            return Result::failure("Insufficient funds for this transaction.");
        }

        if (!$this->debitLimitPolicy->perform($money, $dateTime)) {
            return Result::failure("Limit exceeded.");
        }

        $this->balance = $this->balance->subtract($totalDebit);

        return Result::success();
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function getCurrency(): Currency
    {
        return $this->balance->getCurrency();
    }
}
