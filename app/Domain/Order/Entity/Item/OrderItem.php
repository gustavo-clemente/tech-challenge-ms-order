<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity\Item;

use App\Domain\Order\Exception\Item\InvalidItemQuantityException;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Product\ValueObject\ProductId;

class OrderItem
{
    public function __construct(
        private ProductId $productId,
        private int $quantity,
        private int $priceInCents,
        private ?OrderItemId $id = null,
        private ?int $discountInCents = null
    ){
        $this->validate();
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    private function validate(): void{

        if($this->quantity <= 0){
            throw new InvalidItemQuantityException("An item must have a quantity of at least 1.");
        }
    }
}
