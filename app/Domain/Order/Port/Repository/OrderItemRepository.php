<?php

declare(strict_types=1);

namespace App\Domain\Order\Port\Repository;

use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderId;

interface OrderItemRepository
{
    public function addOrderItems(OrderId $orderId, OrderItemCollection $orderItems): ?Order;
    public function removeOrderItem(OrderId $orderId, OrderItemCollection $orderItems): ?Order;
}
