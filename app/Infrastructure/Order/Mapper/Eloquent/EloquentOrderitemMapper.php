<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Mapper\Eloquent;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Product\ValueObject\ProductId;
use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use Illuminate\Database\Eloquent\Collection;

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

    public function mapToModel(OrderItem $orderItem): OrderItemModel
    {
        return new OrderItemModel([
            'id' => $orderItem->getId()->getIdentifier(),
            'product_id' => $orderItem->getProductId()->getIdentifier(),
            'quantity' => $orderItem->getQuantity(),
            'price_in_cents' => $orderItem->getPriceInCents(),
            'discount_in_cents' => $orderItem->getDiscountInCents(),
        ]);
    }

    public function mapToModelCollection(OrderItemCollection $orderItemCollection): Collection
    {
        $orderItemsModel = [];

        foreach( $orderItemCollection as $orderItem ) {
            $orderItemsModel[] = $this->mapToModel($orderItem);
        }

        return new Collection($orderItemsModel);
    }
}
