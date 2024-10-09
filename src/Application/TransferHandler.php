<?php

declare(strict_types=1);

namespace App\Application;

use App\Common\ClockInterface;
use App\Domain\Model\Payment;
use App\Domain\Model\Value\BankAccountIdentifier;
use App\Domain\Model\Value\Money;
use App\Domain\Model\Value\TransactionFee;
use App\Domain\Service\TransferOperation;

class TransferHandler
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly BankAccountRepository $bankAccountRepository
    ) {
    }

    /**
     * @transactional
     */
    public function handleTransfer(BankAccountIdentifier $sourceId, BankAccountIdentifier $destinationId, Money $amount): array /* HttpResponse */
    {
        $operation = $this->getOperation();

        $source = $this->bankAccountRepository->find($sourceId);
        $destination = $this->bankAccountRepository->find($destinationId);
        $payment = new Payment($amount);

        $result = $operation->perform($source, $destination, $payment);

        if ($result->isSuccess()) {
            return ['HttpResponse' => 'success'];
        }
        return ['HttpResponse' => 'failure', 'reason' => $result->getReason()];

    }

    /**
     * There we load TransferOperation, and add transaction fee.
     * As requirements said about fixed TransactionFee so it can be loaded from configuration there.
     * Otherwise, there is place to implement some TransactionFee chose strategy.
     */
    private function getOperation(): TransferOperation
    {
        return new TransferOperation($this->clock, TransactionFee::percent(0.5));
    }
}
