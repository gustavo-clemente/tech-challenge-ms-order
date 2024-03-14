<?php

declare(strict_types=1);

namespace App\Infrastructure\Product\Adapter\MsAdapter;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Port\MsAdapter\ProductMsAdapter;
use App\Domain\Product\Entity\ProductCollection;
use App\Domain\Product\Entity\ProductIdCollection;
use App\Domain\Product\ValueObject\ProductCategoryId;
use Exception;

class MockProductMsAdapter implements ProductMsAdapter
{
    public function getProductsById(ProductIdCollection $productIds) : ProductCollection
    {   
        $products = [];
        foreach($productIds as $productid){
            $products[] = new Product(
                productId: $productid,
                categoryId: new ProductCategoryId('1'),
                name: "Mock Product",
                priceInCents: 250000
            );
        }

        return new ProductCollection($products);
    }
}
