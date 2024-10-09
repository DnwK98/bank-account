<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Common\ClockInterface;
use App\Common\Result;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Payment;
use App\Domain\Model\Value\TransactionFee;

/**
 * There is transactional implementation, with 2 aggregates in transaction.
 *
 * We can move from transactional consistency to eventual consistency.
 * It may be implemented using process manager with outbox/inbox pattern,
 * but it may require compensation mechanisms. For example, if we subtract
 * from the source account and the destination account gets blocked before the funds are added,
 * we would need to compensate by returning the value to the source account.
 */
class TransferOperation
{
    public function __construct(
        private ClockInterface $clock,
        private TransactionFee $fee
    ) {
    }

    public function perform(BankAccount $source, BankAccount $destination, Payment $payment): Result
    {
        if (!$source->getCurrency()->equals($payment->getAmount()->getCurrency())) {
            return Result::failure("Source account currency is different than expected");
        }

        if (!$destination->getCurrency()->equals($payment->getAmount()->getCurrency())) {
            return Result::failure("Destination account currency is different than expected");
        }

        $result = $source->debit($payment->getAmount(), $this->clock->now(), $this->fee);
        if ($result->isFailure()) {
            return $result;
        }

        $destination->credit($payment->getAmount());

        return Result::success();
    }
}
