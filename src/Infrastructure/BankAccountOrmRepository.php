<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\BankAccountRepository;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Value\BankAccountIdentifier;

/**
 * There goes implementation from framework or raw PDO
 */
class BankAccountOrmRepository implements BankAccountRepository
{
    public function find(BankAccountIdentifier $id): ?BankAccount
    {
        return null;
    }

    public function add(BankAccount $id)
    {
        // noop
    }
}