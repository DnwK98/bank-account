<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Model\Payment;

interface PaymentRepository
{
    public function find(string /* PaymentId */ $id): ?Payment;

    public function add(Payment $id);
}
