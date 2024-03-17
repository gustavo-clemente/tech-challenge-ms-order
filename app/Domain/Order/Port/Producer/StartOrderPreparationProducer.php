<?php

declare(strict_types=1);

namespace App\Domain\Order\Port\Producer;

use App\Domain\Order\Entity\Order;

interface StartOrderPreparationProducer
{
    public function publish(Order $order): void;
}

