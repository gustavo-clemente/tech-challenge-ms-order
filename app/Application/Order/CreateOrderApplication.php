<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Application\Order\Input\CreateOrderInput;
use App\Application\Order\Output\OrderOutput;
use App\Domain\Order\Service\OrderService;

class CreateOrderApplication{
    public function __construct(
        private OrderService $orderService
    ) {

    }

    public function createOrder(CreateOrderInput $createOrderInput): OrderOutput
    {
        $order = $this->orderService->createOrder($createOrderInput->getOrder());

        return new OrderOutput($order);
    }
}
