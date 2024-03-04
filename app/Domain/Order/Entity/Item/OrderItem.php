<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity\Item;

use App\Domain\Order\Exception\InvalidItemQuantityException;
use App\Domain\Product\Entity\ProductId;

class OrderItem implements
{
    public function __construct(
        private ProductId $productId,
        private int $quantity,
        private int $priceInCents,
        private ?OrderItemId $id,
        private ?int $discountInCents
    ){
        $this->validate();
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
