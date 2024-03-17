<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Application\Order\Input\OrderIdInput;
use App\Application\Order\Output\OrderOutput;
use App\Domain\Order\Service\OrderService;

class FinishOrderApplication
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function finishOrder(OrderIdInput $orderIdInput): OrderOutput
    {
        $order = $this->orderService->finishOrderPreparation($orderIdInput->getOrderId());

        return new OrderOutput($order);
    }
}
