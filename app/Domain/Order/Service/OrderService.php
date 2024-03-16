<?php

declare(strict_types=1);

namespace App\Domain\Order\Service;

use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\Exception\CheckoutOrderStatusException;
use App\Domain\Order\Exception\OrderNotCancelableException;
use App\Domain\Order\Exception\OrderNotFoundException;
use App\Domain\Order\Port\Producer\CheckoutOrderProducer;
use App\Domain\Order\Port\Repository\OrderRepository;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Product\Port\MsAdapter\ProductMsAdapter;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductMsAdapter $productMsAdapter,
        private CheckoutOrderProducer $checkoutOrderProducer
    ) {

    }
    
    public function createOrder(Order $order): Order
    {
        $order = $this->populateOrderItemsPrice($order);
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

        $checkoutOrder =  $this->orderRepository->checkoutOrder($order->getOrderId());

        $this->checkoutOrderProducer->publish($checkoutOrder);

        return $checkoutOrder;
    }

    public function cancelOrder(OrderId $orderId): Order
    {
        $order = $this->getOrderById($orderId);

        if(!$this->isOrderValidForCancelation($order)){
            $orderStatus = $order->getOrderDetails()->getOrderStatus()->value;

            throw new OrderNotCancelableException(
                "Unable to proceed with cancel. The order is not in a valid cancel status. Order status: {$orderStatus}"
            );
        }

        return $this->orderRepository->cancelOrder($orderId);
    }

    public function addOrderItems(OrderId $orderId, OrderItemCollection $items): Order
    {
        $order = $this->getOrderById($orderId);

        return $this->orderRepository->addOrderItems($order->getOrderId(), $items);
    }

    public function removeOrderItems(OrderId $orderId, OrderItemIdCollection $items): Order
    {
        $order = $this->getOrderById($orderId);

        return $this->orderRepository->removeOrderItems($order->getOrderId(), $items);
    }

    public function populateOrderItemsPrice(Order $order): Order
    {
        $productIdsCollection = $order->getOrderDetails()->getProductIds();
        $productCollection = $this->productMsAdapter->getProductsById($productIdsCollection);

        foreach($order->getOrderDetails()->getItems() as $item){
            $product = $productCollection->findById($item->getProductId());
            $item->setPriceInCents($product->getPriceInCents());
        }

        return $order;
    }

    private function isOrderValidForCancelation(Order $order): bool
    {
        $validStatus =  $this->getValidStatusForCancel();

        return in_array($order->getOrderDetails()->getOrderStatus(), $validStatus);
    }

    private function getValidStatusForCancel(): array
    {
        return [
            OrderStatus::CREATED,
            OrderStatus::AWAITING_PAYMENT
        ];
    }
}
