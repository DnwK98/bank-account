<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Common\Decimal;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Value\Currency;
use App\Domain\Model\Value\Limit\DailyOperationCountLimitPolicy;
use App\Domain\Model\Value\Money;
use App\Domain\Model\Value\TransactionFee;
use App\Tests\Unit\UnitTestCase;
use DateTimeImmutable;

class BankAccountTest extends UnitTestCase
{
    private BankAccount $account;
    private TransactionFee $fee;
    private DateTimeImmutable $now;

    public function setUp(): void
    {
        parent::setUp();

        $this->account = new BankAccount(
            new DailyOperationCountLimitPolicy(3),
            new Money(Decimal::zero(), $this->currency())
        );

        $this->fee = TransactionFee::percent(0.5);
        $this->now = new DateTimeImmutable();
    }

    public function testDebitDecreasesBalanceWithFee(): void
    {
        // Given
        $initialDeposit = new Money(Decimal::ofFloat(1000.00), $this->currency());
        $this->account->credit($initialDeposit);
        $paymentAmount = new Money(Decimal::ofFloat(100.00), $this->currency());

        // When
        $this->account->debit($paymentAmount, $this->now, $this->fee);

        // Then
        $this->assertEquals('899.5000 USD', $this->account->getBalance()->__toString());
    }

    public function testUnableToDebitMoreThanDeposit(): void
    {
        // Given
        $initialDeposit = new Money(Decimal::ofFloat(200.00), $this->currency());
        $this->account->credit($initialDeposit);
        $paymentAmount = new Money(Decimal::ofFloat(210.00), $this->currency());

        // When
        $result = $this->account->debit($paymentAmount, $this->now, $this->fee);

        // Then
        $this->assertTrue($result->isFailure());
        $this->assertEquals('200.0000 USD', $this->account->getBalance()->__toString());
    }

    public function testDebitOperationsAreLimited(): void
    {
        // Given
        $initialDeposit = new Money(Decimal::ofFloat(1000.00), $this->currency());
        $this->account->credit($initialDeposit);
        $paymentAmount = new Money(Decimal::ofFloat(100.00), $this->currency());

        // When
        $this->account->debit($paymentAmount, $this->now, $this->fee);
        $this->account->debit($paymentAmount, $this->now, $this->fee);
        $result = $this->account->debit($paymentAmount, $this->now, $this->fee);
        // Then
        $this->assertTrue($result->isSuccess());

        // When
        $result = $this->account->debit($paymentAmount, $this->now, $this->fee);
        // Then
        $this->assertTrue($result->isFailure());

        // When
        $nextDay = $this->now->modify('+1 day');
        $result = $this->account->debit($paymentAmount, $nextDay, $this->fee);
        $this->assertTrue($result->isSuccess());
    }

    public function testOperationsAreNotAllowedWithDifferentCurrencies(): void
    {
        // Given
        $initialDeposit = new Money(Decimal::ofFloat(1000.00), $this->currency());
        $this->account->credit($initialDeposit);
        $paymentAmount = new Money(Decimal::ofFloat(100.00), $this->currency('PLN'));

        // When
        $result = $this->account->debit($paymentAmount, $this->now, $this->fee);
        // Then
        $this->assertTrue($result->isFailure());

        // When
        $result = $this->account->credit($paymentAmount);
        // Then
        $this->assertTrue($result->isFailure());
    }

    private function currency(string $code = 'USD'): Currency
    {
        return new Currency($code);
    }
}