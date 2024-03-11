<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Adapter\Repository\Eloquent;

use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\Port\Repository\OrderRepository;
use App\Domain\Order\ValueObject\OrderId;
use App\Infrastructure\Order\Mapper\Eloquent\EloquentOrderitemMapper;
use App\Infrastructure\Order\Mapper\Eloquent\EloquentOrderMapper;
use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use App\Infrastructure\Order\Model\Eloquent\OrderModel;
use Illuminate\Support\Facades\DB;

class EloquentOrderRepository implements OrderRepository
{
    public function __construct(
        private EloquentOrderMapper $eloquentOrderMapper,
        private EloquentOrderitemMapper $eloquentOrderitemMapper
    ) {
    }
    public function createOrder(Order $order): Order
    {
        DB::beginTransaction();

        $orderItemsCollection = $order->getOrderDetails()->getItems();

        $orderModel = $this->eloquentOrderMapper->mapToModel($order);
        $orderModel->save();

        $orderId = new OrderId($orderModel->id);

        $orderItemsModelCollection = $this->eloquentOrderitemMapper
            ->mapToModelCollection($orderId, $orderItemsCollection);

        OrderItemModel::insert($orderItemsModelCollection->toArray());

        DB::commit();

        return $this->eloquentOrderMapper->mapToDomain($orderModel);
    }

    public function getOrderById(OrderId $orderId): ?Order
    {
        $orderModel = OrderModel::find($orderId->getIdentifier());

        if(is_null($orderModel)){
            return null;
        }

        return $this->eloquentOrderMapper->mapToDomain($orderModel);
    }

    public function checkoutOrder(OrderId $orderId): ?Order
    {
        return $this->updateOrderStatus($orderId, OrderStatus::AWAITING_PAYMENT);
    }

    public function cancelOrder(OrderId $orderId): Order
    {
        return $this->updateOrderStatus($orderId, OrderStatus::CANCELLED);
    }

    public function addOrderItems(OrderId $orderId, OrderItemCollection $orderItems): ?Order
    {
        $orderModel = OrderModel::find($orderId->getIdentifier());

        if(is_null($orderModel)){
            return null;
        }

        $orderItemsModelCollection = $this->eloquentOrderitemMapper
            ->mapToModelCollection($orderId, $orderItems);

        OrderItemModel::insert($orderItemsModelCollection->toArray());

        return $this->eloquentOrderMapper->mapToDomain($orderModel);
    }

    public function removeOrderItems(OrderId $orderId, OrderItemIdCollection $orderItems): ?Order
    {
        $orderModel = OrderModel::find($orderId->getIdentifier());

        if(is_null($orderModel)){
            return null;
        }

        $orderItemsModelCollection = $this->eloquentOrderitemMapper
            ->mapToModelIdCollection($orderItems);

        OrderItemModel::destroy($orderItemsModelCollection);

        return $this->eloquentOrderMapper->mapToDomain($orderModel);
    }

    private function updateOrderStatus(OrderId $orderId, OrderStatus $orderStatus): ?Order
    {
        $orderModel = OrderModel::find($orderId->getIdentifier());

        if(is_null($orderModel)){
            return null;
        }

        $orderModel->status = $orderStatus->value;
        $orderModel->save();
        
        return $this->eloquentOrderMapper->mapToDomain($orderModel);
    }
}
