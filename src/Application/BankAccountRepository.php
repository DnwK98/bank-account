<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Model\BankAccount;
use App\Domain\Model\Value\BankAccountIdentifier;

// Assume change tracking and UOW patterns
// otherwise we can add save method and call it
interface BankAccountRepository
{
    public function find(BankAccountIdentifier $id): ?BankAccount;

    public function add(BankAccount $id);
}
