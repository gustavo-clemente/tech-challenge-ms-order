<?php

declare(strict_types=1);

namespace App\Domain\Product\Entity;
use App\Domain\Shared\Collection;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\ValueObject\ProductId;

/** @method Product current() */
class ProductCollection extends Collection
{
    public function findById(ProductId $productId): ?Product
    {
        foreach($this as $product){
            if ($product->getProductId() === $productId) {
                return $product;
            }
        }

        return null;
    }
}
