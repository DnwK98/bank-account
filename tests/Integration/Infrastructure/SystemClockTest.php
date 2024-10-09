<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure;

use App\Infrastructure\SystemClock;
use App\Tests\Integration\IntegrationTestCase;

/**
 * Test infrastructure only, not whole application stack,
 * because we have tested domain with mocks, or stubs, or input-output based
 */
class SystemClockTest extends IntegrationTestCase
{
    public function testClockReturnsToday()
    {
        // Given
        $clock = new SystemClock();

        // When
        $result = $clock->now()->format('Y-m-d');

        // Then
        $expected = (new \DateTimeImmutable())->format('Y-m-d');
        $this->assertEquals($expected, $result);
    }
}