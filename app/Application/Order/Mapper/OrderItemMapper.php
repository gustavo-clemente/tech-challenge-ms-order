<?php

declare(strict_types=1);

namespace App\Application\Order\Mapper;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Product\ValueObject\ProductId;

class OrderItemMapper
{
    public static function mapToCollection(array $data): OrderItemCollection
    {
        $orderItems = [];

        foreach($data as $item){
            $orderItems[] = new OrderItem(
                productId: new ProductId($item['productId']),
                quantity: $item['quantity']
            );
        }

        return new OrderItemCollection($orderItems);
    }

    public static function mapToIdCollection(array $data): OrderItemIdCollection
    {
        $orderItemIds = [];

        foreach($data as $item){
            $orderItemIds[] = new OrderItemId($item['itemId']);
        }

        return new OrderItemIdCollection($orderItemIds);
    }
}
