<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Mapper\Eloquent;

use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Store\Entity\StoreId;
use App\Infrastructure\Order\Model\Eloquent\OrderModel;
use Carbon\Carbon;

class EloquentOrderMapper
{
    public function __construct(
        private EloquentOrderitemMapper $orderitemMapper
    ) {

    }
    public function mapToDomain(OrderModel $orderModel): Order
    {
        $previsionDeliveryDate = $orderModel->prevision_delivery_date;

        return new Order(
            orderId: new OrderId($orderModel->id),
            orderDetails: new OrderDetails(
                storeId: new StoreId($orderModel->store_id),
                customerId: new CustomerId($orderModel->customer_id),
                items: $this->orderitemMapper->mapToDomainCollection($orderModel->items),
                orderStatus: OrderStatus::from($orderModel->status),
                previsionDeliveryDate: $previsionDeliveryDate ?
                                       new \DateTime($previsionDeliveryDate) :
                                       null
            ),
            createdAt: new \DateTime($orderModel->created_at->toDateTimeString()),
        );
    }

    public function mapToModel(Order $order): OrderModel
    {
        $orderStatus = $order->getOrderDetails()->getOrderStatus();

        return new OrderModel([
            "id" => $order->getOrderId()?->getIdentifier(),
            "store_id" => $order->getOrderDetails()->getStoreId()->getIdentifier(),
            "customer_id" => $order->getOrderDetails()->getCustomerId()->getIdentifier(),
            "status" => $orderStatus ? $orderStatus->value : OrderStatus::CREATED->value,
            "prevision_delivery_date" =>  $order->getOrderDetails()->getPrevisionDeliveryDate() ?
                                          Carbon::parse($order->getOrderDetails()->getPrevisionDeliveryDate()) :
                                          null
        ]);
    }
    
}
