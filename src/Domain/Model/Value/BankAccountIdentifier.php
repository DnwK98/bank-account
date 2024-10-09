<?php

declare(strict_types=1);

namespace App\Domain\Model\Value;

use Stringable;

readonly class BankAccountIdentifier implements Stringable
{
    private function __construct(
        private string $id
    ) {
    }

    public static function ofString(string $id): self
    {
        return new self($id);
    }


    public function __toString(): string
    {
        return $this->id;
    }
}
