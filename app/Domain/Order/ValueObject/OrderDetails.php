<?php

declare(strict_types= 1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\Exception\Item\ItemWithouPriceException;
use App\Domain\Product\Entity\ProductIdCollection;
use App\Domain\Store\Entity\StoreId;
use DateTime;

class OrderDetails implements \JsonSerializable
{
    public function __construct(
        private StoreId $storeId,
        private OrderItemCollection $items,
        private ?OrderStatus $orderStatus = null,
        private ?CustomerId $customerId = null,
        private ?DateTime $previsionDeliveryDate = null,
    ){
        
    }

    public function getItems(): OrderItemCollection
    {
        return $this->items;
    }

    public function getTotalItems(): int
    {
        return $this->items->count();
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function getStoreId(): StoreId
    {
        return $this->storeId;
    }

    public function getCustomerId(): ?CustomerId
    {
        return $this->customerId;
    }

    public function getPrevisionDeliveryDate(): ?DateTime
    {
        return $this->previsionDeliveryDate;
    }

    public function getProductIds(): ProductIdCollection
    {
        $productIds = [];

        foreach($this->items as $item) {
            $productIds[] = $item->getProductId();
        }

        return new ProductIdCollection($productIds);
    }

    public function getTotalAmountInCents(): int
    {
        $totalAmount = 0;
        $totalDiscountedPrice = 0;

        foreach($this->items as $item) {
            if(!$item->getPriceInCents()){
                throw new ItemWithouPriceException("Cannot calculate total amount without item price verification", 500);
            }

            $totalAmount += ($item->getPriceInCents() * $item->getQuantity());
            $totalDiscountedPrice += $item->getDiscountInCents() ?? 0;
        }

        return $totalAmount - $totalDiscountedPrice;
    }

    public function getTotalAmountInReal(): float
    {
        $totalAmountInCents = $this->getTotalAmountInCents();

        return $totalAmountInCents / 100;
    }

    public function jsonSerialize(): array
    {
        return [
            'amountInCents' => $this->getTotalAmountInCents(),
            'amountInReal' => $this->getTotalAmountInReal(),
            'storeId' => $this->storeId->getIdentifier(),
            'items' => $this->items->jsonSerialize(),
            'orderStatus' => $this->orderStatus?->value,
            'customerId' => $this->customerId?->getIdentifier(),
            'previsionDeliveryDate' => $this->previsionDeliveryDate?->format('Y-m-d H:i:s')
        ];
    }
}
