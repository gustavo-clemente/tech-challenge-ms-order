<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Mapper\Eloquent;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Product\ValueObject\ProductId;
use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Ramsey\Uuid\Uuid;

class EloquentOrderitemMapper
{
    public function mapToDomain(OrderItemModel $orderItemModel): OrderItem
    {
        return new OrderItem(
            id: new OrderItemId($orderItemModel->id),
            productId: new ProductId($orderItemModel->product_id),
            quantity: $orderItemModel->quantity,
            priceInCents: $orderItemModel->price_in_cents,
            discountInCents: $orderItemModel->discount_in_cents
        );
    }

    public function mapToDomainCollection(Collection $collection): OrderItemCollection
    {
        $orderItems = [];

        foreach ($collection as $orderItemModel) {
            $orderItems[] = $this->mapToDomain($orderItemModel);
        }

        return new OrderItemCollection($orderItems);
    }

    public function mapToModel(OrderId $orderId, OrderItem $orderItem): OrderItemModel
    {
        $orderItemId = $orderItem->getId();

        return new OrderItemModel([
            'id' => $orderItemId ? $orderItemId->getIdentifier() : Uuid::uuid4(),
            'order_id' => $orderId->getIdentifier(),
            'product_id' => $orderItem->getProductId()->getIdentifier(),
            'quantity' => $orderItem->getQuantity(),
            'price_in_cents' => $orderItem->getPriceInCents(),
            'discount_in_cents' => $orderItem->getDiscountInCents(),
            'created_at' => Carbon::now()
        ]);
    }

    public function mapToModelCollection(OrderId $orderId, OrderItemCollection $orderItemCollection): Collection
    {
        $orderItemsModel = [];

        foreach( $orderItemCollection as $orderItem ) {
            $orderItemsModel[] = $this->mapToModel($orderId, $orderItem);
        }

        return new Collection($orderItemsModel);
    }

    public function mapToModelIdCollection(OrderItemIdCollection $orderItemIdCollection): Collection
    {
        $orderItemIdsModel = [];

        foreach( $orderItemIdCollection as $orderItemId ) {
            $orderItemIdsModel[] = new OrderItemModel([
                'id' => $orderItemId->getIdentifier()
            ]);
        }

        return new Collection($orderItemIdsModel);
    }
}
