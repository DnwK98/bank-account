<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Tests\Unit\UnitTestCase;
use App\Common\ClockInterface;
use App\Common\Result;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Payment;
use App\Domain\Model\Value\Currency;
use App\Common\Decimal;
use App\Domain\Model\Value\Money;
use App\Domain\Model\Value\TransactionFee;
use App\Domain\Service\TransferOperation;
use PHPUnit\Framework\MockObject\MockObject;

class TransferOperationTest extends UnitTestCase
{
    /** @var ClockInterface|MockObject */
    private $clockMock;

    /** @var TransactionFee */
    private TransactionFee $transactionFee;

    /** @var TransferOperation */
    private TransferOperation $transferOperation;

    public function setUp(): void
    {
        parent::setUp();

        $this->clockMock = $this->createMock(ClockInterface::class);
        $this->transactionFee = TransactionFee::percent(0.5); // 0.5% fee
        $this->transferOperation = new TransferOperation($this->clockMock, $this->transactionFee);
    }

    public function testSuccessfulTransfer(): void
    {
        // Given
        $currency = new Currency('USD');
        $amount = new Money(Decimal::ofFloat(100.00), $currency);
        $payment = new Payment($amount);

        $sourceAccount = $this->createMock(BankAccount::class);
        $destinationAccount = $this->createMock(BankAccount::class);

        // Mock current time
        $currentTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->clockMock->method('now')->willReturn($currentTime);

        // Mock currency checks
        $sourceAccount->method('getCurrency')->willReturn($currency);
        $destinationAccount->method('getCurrency')->willReturn($currency);

        // Mock debit operation on source account
        $sourceAccount->expects($this->once())
            ->method('debit')
            ->with($amount, $currentTime, $this->transactionFee)
            ->willReturn(Result::success());

        // Mock credit operation on destination account
        $destinationAccount->expects($this->once())
            ->method('credit')
            ->with($amount);

        // When
        $result = $this->transferOperation->perform($sourceAccount, $destinationAccount, $payment);

        // Then
        $this->assertTrue($result->isSuccess());
    }

    public function testSourceAccountCurrencyMismatch(): void
    {
        // Given
        $paymentCurrency = new Currency('USD');
        $sourceCurrency = new Currency('EUR');
        $destinationCurrency = new Currency('USD');

        $amount = new Money(Decimal::ofFloat(100.00), $paymentCurrency);
        $payment = new Payment($amount);

        $sourceAccount = $this->createMock(BankAccount::class);
        $destinationAccount = $this->createMock(BankAccount::class);

        // Mock currency checks
        $sourceAccount->method('getCurrency')->willReturn($sourceCurrency);
        $destinationAccount->method('getCurrency')->willReturn($destinationCurrency);

        // When
        $result = $this->transferOperation->perform($sourceAccount, $destinationAccount, $payment);

        // Then
        $this->assertTrue($result->isFailure());
        $this->assertEquals("Source account currency is different than expected", $result->getReason());
    }

    public function testDestinationAccountCurrencyMismatch(): void
    {
        // Given
        $paymentCurrency = new Currency('USD');
        $sourceCurrency = new Currency('USD');
        $destinationCurrency = new Currency('EUR');

        $amount = new Money(Decimal::ofFloat(100.00), $paymentCurrency);
        $payment = new Payment($amount);

        $sourceAccount = $this->createMock(BankAccount::class);
        $destinationAccount = $this->createMock(BankAccount::class);

        // Mock currency checks
        $sourceAccount->method('getCurrency')->willReturn($sourceCurrency);
        $destinationAccount->method('getCurrency')->willReturn($destinationCurrency);

        // When
        $result = $this->transferOperation->perform($sourceAccount, $destinationAccount, $payment);

        // Then
        $this->assertTrue($result->isFailure());
        $this->assertEquals("Destination account currency is different than expected", $result->getReason());
    }

    public function testDebitOperationFailsDueToInsufficientFunds(): void
    {
        // Given
        $currency = new Currency('USD');
        $amount = new Money(Decimal::ofFloat(1000.00), $currency);
        $payment = new Payment($amount);

        $sourceAccount = $this->createMock(BankAccount::class);
        $destinationAccount = $this->createMock(BankAccount::class);

        // Mock current time
        $currentTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->clockMock->method('now')->willReturn($currentTime);

        // Mock currency checks
        $sourceAccount->method('getCurrency')->willReturn($currency);
        $destinationAccount->method('getCurrency')->willReturn($currency);

        // Mock debit operation failure
        $failureResult = Result::failure("Insufficient funds");
        $sourceAccount->expects($this->once())
            ->method('debit')
            ->with($amount, $currentTime, $this->transactionFee)
            ->willReturn($failureResult);

        // When
        $result = $this->transferOperation->perform($sourceAccount, $destinationAccount, $payment);

        // Then
        $this->assertTrue($result->isFailure());
        $this->assertEquals("Insufficient funds", $result->getReason());

        // Ensure destination account was not credited
        $destinationAccount->expects($this->never())->method('credit');
    }

    public function testDebitOperationFailsDueToDailyLimitExceeded(): void
    {
        // Given
        $currency = new Currency('USD');
        $amount = new Money(Decimal::ofFloat(50.00), $currency);
        $payment = new Payment($amount);

        $sourceAccount = $this->createMock(BankAccount::class);
        $destinationAccount = $this->createMock(BankAccount::class);

        // Mock current time
        $currentTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->clockMock->method('now')->willReturn($currentTime);

        // Mock currency checks
        $sourceAccount->method('getCurrency')->willReturn($currency);
        $destinationAccount->method('getCurrency')->willReturn($currency);

        // Mock debit operation failure due to daily limit exceeded
        $failureResult = Result::failure("Daily limit exceeded");
        $sourceAccount->expects($this->once())
            ->method('debit')
            ->with($amount, $currentTime, $this->transactionFee)
            ->willReturn($failureResult);

        // When
        $result = $this->transferOperation->perform($sourceAccount, $destinationAccount, $payment);

        // Then
        $this->assertTrue($result->isFailure());
        $this->assertEquals("Daily limit exceeded", $result->getReason());

        // Ensure destination account was not credited
        $destinationAccount->expects($this->never())->method('credit');
    }

    public function testCreditOperationIsPerformedAfterSuccessfulDebit(): void
    {
        // Given
        $currency = new Currency('USD');
        $amount = new Money(Decimal::ofFloat(200.00), $currency);
        $payment = new Payment($amount);

        $sourceAccount = $this->createMock(BankAccount::class);
        $destinationAccount = $this->createMock(BankAccount::class);

        // Mock current time
        $currentTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->clockMock->method('now')->willReturn($currentTime);

        // Mock currency checks
        $sourceAccount->method('getCurrency')->willReturn($currency);
        $destinationAccount->method('getCurrency')->willReturn($currency);

        // Mock successful debit operation
        $sourceAccount->expects($this->once())
            ->method('debit')
            ->with($amount, $currentTime, $this->transactionFee)
            ->willReturn(Result::success());

        // Mock credit operation on destination account
        $destinationAccount->expects($this->once())
            ->method('credit')
            ->with($amount);

        // When
        $result = $this->transferOperation->perform($sourceAccount, $destinationAccount, $payment);

        // Then
        $this->assertTrue($result->isSuccess());
    }
}