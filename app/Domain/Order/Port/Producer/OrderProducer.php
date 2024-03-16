<?php

declare(strict_types=1);

namespace App\Domain\Order\Port\Producer;

use App\Domain\Order\Entity\Order;

interface OrderProducer
{
    public function publishOrderForPayment(Order $order): bool;
    public function publishOrderForPreparation(Order $order): bool;
}
