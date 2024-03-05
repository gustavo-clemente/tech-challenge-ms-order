<?php

declare(strict_types=1);

namespace App\Domain\Order\Service;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\Exception\CheckoutOrderStatusException;
use App\Domain\Order\Exception\OrderNotCancelableException;
use App\Domain\Order\Exception\OrderNotFoundException;
use App\Domain\Order\Port\Repository\OrderRepository;
use App\Domain\Order\ValueObject\OrderId;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {

    }
    
    public function createOrder(Order $order): Order
    {
        return $this->orderRepository->createOrder($order);
    }

    public function getOrderById(OrderId $orderId): Order
    {
        $order = $this->orderRepository->getOrderById($orderId);

        if(is_null($order)){
            throw new OrderNotFoundException();
        }

        return $order;
    }

    public function checkoutOrder(OrderId $orderId): Order
    {
        $order = $this->getOrderById($orderId);
        
        if($order->getOrderDetails()->getOrderStatus() !== OrderStatus::CREATED){
            throw new CheckoutOrderStatusException(
                "Unable to proceed with checkout. The order is not in the 'created' status."
            );
        }

        return $this->orderRepository->checkoutOrder($order->getOrderId());
    }

    public function cancelOrder(OrderId $orderId): Order
    {
        $order = $this->getOrderById($orderId);

        if($order->getOrderDetails()->getOrderStatus() !== OrderStatus::CREATED){
            throw new OrderNotCancelableException(
                "Unable to proceed with cancel. The order is not in the 'created' status."
            );
        }

        return $this->cancelOrder($orderId);
    }
}
