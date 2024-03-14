<?php

declare(strict_types=1);

namespace App\Domain\Product\Service;

use App\Domain\Product\Entity\ProductCollection;
use App\Domain\Product\Entity\ProductIdCollection;
use App\Domain\Product\Port\MsAdapter\ProductMsAdapter;

class ProductService
{
    public function __construct(
        private ProductMsAdapter $adapter
    ) {

    }

    public function getProductsByIds(ProductIdCollection $productIds): ProductCollection
    {
        return $this->adapter->getProductsById($productIds);
    }
}

