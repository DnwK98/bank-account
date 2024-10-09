<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Value;

use App\Common\Decimal;
use App\Domain\Model\Value\Currency;
use App\Domain\Model\Value\Money;
use App\Domain\Model\Value\TransactionFee;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TransactionFeeTest extends TestCase
{
    public function testApplyZeroFee(): void
    {
        $amount = Decimal::ofFloat(1000.00);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $fee = TransactionFee::zero();
        $result = $fee->apply($money);

        $this->assertTrue($money->equals($result));
    }

    public function testApplyPercentFee(): void
    {
        $amount = Decimal::ofFloat(1000.00);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $feePercent = 0.5;
        $fee = TransactionFee::percent($feePercent);
        $result = $fee->apply($money);

        $expectedAmount = $amount->multiply(Decimal::one()->add(Decimal::ofFloat($feePercent / 100)));
        $expectedMoney = new Money($expectedAmount, $currency);

        $this->assertTrue($expectedMoney->equals($result));
    }

    public function testApplyHighPercentFee(): void
    {
        $amount = Decimal::ofFloat(1000.00);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $feePercent = 15.0;
        $fee = TransactionFee::percent($feePercent);
        $result = $fee->apply($money);

        $expectedAmount = $amount->multiply(Decimal::one()->add(Decimal::ofFloat($feePercent / 100)));
        $expectedMoney = new Money($expectedAmount, $currency);

        $this->assertTrue($expectedMoney->equals($result));
    }

    public function testApplyNegativeFeeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $amount = Decimal::ofFloat(1000.00);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $feePercent = -5.0;
        $fee = TransactionFee::percent($feePercent);
        $fee->apply($money);
    }

    public function testApplyFeeResultsInCorrectAmount(): void
    {
        $amount = Decimal::ofFloat(1000.00);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $feePercent = 2.5;
        $fee = TransactionFee::percent($feePercent);
        $result = $fee->apply($money);

        $expectedAmount = Decimal::ofFloat(1025.00); // 1000 + 2.5% of 1000
        $expectedMoney = new Money($expectedAmount, $currency);

        $this->assertTrue($expectedMoney->equals($result));
    }

    public function testApplyFeeOnZeroAmount(): void
    {
        $amount = Decimal::zero();
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $feePercent = 5.0;
        $fee = TransactionFee::percent($feePercent);
        $result = $fee->apply($money);

        $expectedAmount = Decimal::zero();
        $expectedMoney = new Money($expectedAmount, $currency);

        $this->assertTrue($expectedMoney->equals($result));
    }
}