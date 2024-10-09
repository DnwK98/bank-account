<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Application\BankAccountRepository;
use App\Application\TransferHandler;
use App\Common\Decimal;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Value\BankAccountIdentifier;
use App\Domain\Model\Value\Currency;
use App\Domain\Model\Value\Limit\DailyOperationCountLimitPolicy;
use App\Domain\Model\Value\Money;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Mock\FixedClock;


/**
 * Test whole application stack, only green path and main errors
 */
class TransferOperationTest extends IntegrationTestCase
{
    private TransferHandler $transferHandler;

    public function setUp(): void
    {
        $this->transferHandler = new TransferHandler(new FixedClock(), $this->repository());
    }

    public function testTransfer(): void
    {
        $result = $this->transferHandler->handleTransfer(
            BankAccountIdentifier::ofString("a"),
            BankAccountIdentifier::ofString("b"),
            new Money(Decimal::ofFloat(100), new Currency('USD'))
        );

        $this->assertTrue(true);
        // Assert HTTP response
        // Or add additional layer for HTTP mapping
    }

    /**
     * Normally we should load repository from some container and test integration with database
     */
    private function repository(): BankAccountRepository
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $mock->method('find')->willReturn(new BankAccount(
            new DailyOperationCountLimitPolicy(3),
            new Money(Decimal::ofFloat(1000), new Currency('USD'))
        ));

        return $mock;
    }
}