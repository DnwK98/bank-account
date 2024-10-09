<?php

declare(strict_types=1);

namespace App\Common;

class Result
{
    public const SUCCESS = 'success';
    public const FAILURE = 'failure';

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public static function failure(string $reason): self
    {
        return new self(self::FAILURE, $reason);
    }

    public function __construct(
        public readonly string $result,
        public readonly ?string $reason = null,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->result === self::SUCCESS;
    }

    public function isFailure(): bool
    {
        return $this->result === self::FAILURE;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
