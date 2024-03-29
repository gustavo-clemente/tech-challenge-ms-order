<?php

declare(strict_types=1);

namespace App\Domain\Order\Port\Repository;

use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderId;

interface OrderRepository
{
    public function createOrder(Order $order): Order;
    public function getOrderById(OrderId $orderId): ?Order;
    public function checkoutOrder(OrderId $orderId): ?Order;
    public function cancelOrder(OrderId $orderId): ?Order;
    public function addOrderItems(OrderId $orderId, OrderItemCollection $orderItems): ?Order;
    public function removeOrderItems(OrderId $orderId, OrderItemIdCollection $orderItemsId): ?Order;
}
