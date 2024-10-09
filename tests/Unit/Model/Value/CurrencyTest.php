<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Value;

use App\Domain\Model\Value\Currency;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;

class CurrencyTest extends UnitTestCase
{
    public function testConstructWithValidCode(): void
    {
        $currency = new Currency('USD');
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('USD', (string)$currency);
    }

    public function testConstructConvertsCodeToUpperCase(): void
    {
        $currency = new Currency('usd');
        $this->assertEquals('USD', (string)$currency);
    }

    public function testConstructWithInvalidCodeLengthThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Currency('US');
    }

    public function testConstructWithEmptyCodeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Currency('');
    }

    public function testConstructWithMoreThanThreeCharactersThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Currency('USDA');
    }

    public function testEqualsReturnsTrueForSameCodes(): void
    {
        $currency1 = new Currency('USD');
        $currency2 = new Currency('USD');
        $this->assertTrue($currency1->equals($currency2));
    }

    public function testEqualsIsCaseInsensitive(): void
    {
        $currency1 = new Currency('Usd');
        $currency2 = new Currency('usd');
        $this->assertTrue($currency1->equals($currency2));
    }

    public function testEqualsReturnsFalseForDifferentCodes(): void
    {
        $currency1 = new Currency('USD');
        $currency2 = new Currency('EUR');
        $this->assertFalse($currency1->equals($currency2));
    }

    public function testToStringReturnsCurrencyCode(): void
    {
        $currency = new Currency('eur');
        $this->assertEquals('EUR', $currency->__toString());
    }
}