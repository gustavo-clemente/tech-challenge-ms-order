<?php

declare(strict_types=1);

namespace App\Domain\Product\Port\MsAdapter;

use App\Domain\Product\Entity\ProductCollection;
use App\Domain\Product\Entity\ProductIdCollection;

interface ProductMsAdapter
{
    public function getProductsById(ProductIdCollection $productIds): ProductCollection;
}
