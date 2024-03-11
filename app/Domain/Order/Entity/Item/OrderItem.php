<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity\Item;

use App\Domain\Order\Exception\Item\InvalidItemQuantityException;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Product\ValueObject\ProductId;

class OrderItem implements \JsonSerializable
{
    public function __construct(
        private ProductId $productId,
        private int $quantity,
        private ?int $priceInCents = null,
        private ?OrderItemId $id = null,
        private ?int $discountInCents = null
    ){
        $this->validate();
    }

    public function getId(): ?OrderItemId
    {
        return $this->id;
    }
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPriceInCents(): int
    {
        return $this->priceInCents;
    }

    public function getDiscountInCents(): ?int
    {
        return $this->discountInCents;
    }

    private function validate(): void{

        if($this->quantity <= 0){
            throw new InvalidItemQuantityException("An item must have a quantity of at least 1.");
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'orderItemId' => $this->id?->getIdentifier(),
            'productId' => $this->productId->getIdentifier(),
            'quantity' => $this->quantity,
            'priceInCents' => $this->priceInCents,
            'discountInCents' => $this->discountInCents
        ];
    }
}
