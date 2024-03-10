<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Application\Order\Input\OrderItemsInput;
use App\Application\Order\Output\OrderOutput;
use App\Domain\Order\Service\OrderService;

class RemoveOrderItemsApplication
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function removeOrderItems(OrderItemsInput $orderItemsInput): OrderOutput
    {
        $order = $this->orderService->removeOrderItems(
            $orderItemsInput->getOrderId(), 
            $orderItemsInput->getOrderItems()
        );

        return new OrderOutput($order);
    }
}
