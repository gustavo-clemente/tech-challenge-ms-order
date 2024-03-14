<?php

declare(strict_types= 1);

namespace App\Domain\Product\Entity;

use App\Domain\Product\ValueObject\ProductCategoryId;
use App\Domain\Product\ValueObject\ProductId;

class Product
{
    public function __construct(
        private ProductId $productId,
        private ProductCategoryId $categoryId,
        private string $name,
        private int $priceInCents,
        private ?string $description = null,
        private ?string $imagePath = null
    ) {
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getPriceInCents(): int
    {
        return $this->priceInCents;
    }
}
