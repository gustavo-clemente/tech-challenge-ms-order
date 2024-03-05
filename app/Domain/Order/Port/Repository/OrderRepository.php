<?php

declare(strict_types=1);

namespace App\Domain\Order\Port\Repository;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderId;

interface OrderRepository
{
    public function createOrder(Order $order): Order;
    public function getOrderById(OrderId $orderId): ?Order;
    public function checkoutOrder(OrderId $orderId): Order;
    public function cancelOrder(OrderId $orderId): ?Order;
}
