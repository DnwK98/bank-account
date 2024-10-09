<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Value;

use App\Common\Decimal;
use App\Domain\Model\Value\Currency;
use App\Domain\Model\Value\Money;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;

class MoneyTest extends UnitTestCase
{
    public function testConstructWithPositiveAmount(): void
    {
        $amount = Decimal::ofFloat(1000);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals('1 000.0000 USD', (string)$money);
    }

    public function testConstructWithZeroAmount(): void
    {
        $amount = Decimal::zero();
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals('0.0000 USD', (string)$money);
    }

    public function testConstructWithNegativeAmountThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Amount must be positive.");

        $amount = Decimal::ofFloat(-1000);
        $currency = new Currency('USD');
        new Money($amount, $currency);
    }

    public function testAddWithSameCurrency(): void
    {
        $amount1 = Decimal::ofFloat(1000);
        $currency = new Currency('USD');
        $money1 = new Money($amount1, $currency);

        $amount2 = Decimal::ofFloat(500);
        $money2 = new Money($amount2, $currency);

        $result = $money1->add($money2);

        $expectedAmount = Decimal::ofFloat(1500);
        $expectedMoney = new Money($expectedAmount, $currency);

        $this->assertTrue($result->equals($expectedMoney));
    }

    public function testAddWithDifferentCurrencyThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Currency mismatch.");

        $amount1 = Decimal::ofFloat(1000);
        $currency1 = new Currency('USD');
        $money1 = new Money($amount1, $currency1);

        $amount2 = Decimal::ofFloat(500);
        $currency2 = new Currency('EUR');
        $money2 = new Money($amount2, $currency2);

        $money1->add($money2);
    }

    public function testSubtractWithSameCurrency(): void
    {
        $amount1 = Decimal::ofFloat(1000);
        $currency = new Currency('USD');
        $money1 = new Money($amount1, $currency);

        $amount2 = Decimal::ofFloat(300);
        $money2 = new Money($amount2, $currency);

        $result = $money1->subtract($money2);

        $expectedAmount = Decimal::ofFloat(700);
        $expectedMoney = new Money($expectedAmount, $currency);

        $this->assertTrue($result->equals($expectedMoney));
    }

    public function testSubtractWithDifferentCurrencyThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Currency mismatch.");

        $amount1 = Decimal::ofFloat(1000);
        $currency1 = new Currency('USD');
        $money1 = new Money($amount1, $currency1);

        $amount2 = Decimal::ofFloat(500);
        $currency2 = new Currency('EUR');
        $money2 = new Money($amount2, $currency2);

        $money1->subtract($money2);
    }

    public function testMultiply(): void
    {
        $amount = Decimal::ofFloat(1000);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $factor = Decimal::ofFloat(2);
        $result = $money->multiply($factor);

        $expectedAmount = Decimal::ofFloat(2000);
        $expectedMoney = new Money($expectedAmount, $currency);

        $this->assertTrue($result->equals($expectedMoney));
    }

    public function testGteWithSameCurrency(): void
    {
        $amount1 = Decimal::ofFloat(1000);
        $currency = new Currency('USD');
        $money1 = new Money($amount1, $currency);

        $amount2 = Decimal::ofFloat(500);
        $money2 = new Money($amount2, $currency);

        $this->assertTrue($money1->gte($money2));
        $this->assertFalse($money2->gte($money1));

        $amount3 = Decimal::ofFloat(1000);
        $money3 = new Money($amount3, $currency);

        $this->assertTrue($money1->gte($money3));
    }

    public function testGteWithDifferentCurrencyThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Currency mismatch.");

        $amount1 = Decimal::ofFloat(1000);
        $currency1 = new Currency('USD');
        $money1 = new Money($amount1, $currency1);

        $amount2 = Decimal::ofFloat(500);
        $currency2 = new Currency('EUR');
        $money2 = new Money($amount2, $currency2);

        $money1->gte($money2);
    }

    public function testEquals(): void
    {
        $amount1 = Decimal::ofFloat(1000);
        $currency1 = new Currency('USD');
        $money1 = new Money($amount1, $currency1);

        $amount2 = Decimal::ofFloat(1000);
        $currency2 = new Currency('USD');
        $money2 = new Money($amount2, $currency2);

        $this->assertTrue($money1->equals($money2));

        $amount3 = Decimal::ofFloat(500);
        $money3 = new Money($amount3, $currency1);

        $this->assertFalse($money1->equals($money3));

        $currency3 = new Currency('EUR');
        $money4 = new Money($amount1, $currency3);

        $this->assertFalse($money1->equals($money4));
    }

    public function testToString(): void
    {
        $amount = Decimal::ofFloat(1000);
        $currency = new Currency('USD');
        $money = new Money($amount, $currency);

        $expectedString = '1 000.0000 USD';
        $this->assertEquals($expectedString, (string)$money);
    }
}