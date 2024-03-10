<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Application\Order\Input\OrderItemsInput;
use App\Application\Order\Output\OrderOutput;
use App\Domain\Order\Service\OrderService;

class AddOrderItemsApplication
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function addOrderItems(OrderItemsInput $orderItemsInput): OrderOutput
    {
        $order = $this->orderService->addOrderItems($orderItemsInput->getOrderId(), $orderItemsInput->getOrderItems());

        return new OrderOutput($order);
    }
}
