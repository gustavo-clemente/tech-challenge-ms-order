<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Application\Order\Input\OrderIdInput;
use App\Application\Order\Output\OrderOutput;
use App\Domain\Order\Service\OrderService;

class CancelOrderApplication
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function cancelOrder(OrderIdInput $orderIdInput): OrderOutput
    {
        $order = $this->orderService->cancelOrder($orderIdInput->getOrderId());

        return new OrderOutput($order);
    }
}
