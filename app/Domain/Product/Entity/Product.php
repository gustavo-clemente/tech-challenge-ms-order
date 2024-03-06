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
        private ?string $description,
        private ?string $imagePath
    ) {
    }
}
