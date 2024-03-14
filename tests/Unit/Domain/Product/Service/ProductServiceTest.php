<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Product\Service;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductCollection;
use App\Domain\Product\Entity\ProductIdCollection;
use App\Domain\Product\Port\MsAdapter\ProductMsAdapter;
use App\Domain\Product\Service\ProductService;
use App\Domain\Product\ValueObject\ProductCategoryId;
use App\Domain\Product\ValueObject\ProductId;
use Mockery\MockInterface;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    public function test_get_by_ids_returns_product_collection(): void
    {
        $productIdsCollection = new ProductIdCollection([
            new ProductId("1"),
            new ProductId("2"),
            new ProductId("3"),
        ]);

        $productCollection = new ProductCollection([
            new Product(
                productId: new ProductId("1"),
                categoryId: new ProductCategoryId("1"),
                name: "Product 1",
                priceInCents: 20000
            ),
            new Product(
                productId: new ProductId("2"),
                categoryId: new ProductCategoryId("2"),
                name: "Product 2",
                priceInCents: 30000
            ),
            new Product(
                productId: new ProductId("3"),
                categoryId: new ProductCategoryId("3"),
                name: "Product 3",
                priceInCents: 20000
            )
        ]);

        $this->mock(ProductMsAdapter::class, function (MockInterface $mock) use (
            $productIdsCollection, 
            $productCollection
            ): void {
            $mock
              ->shouldReceive("getProductsById")
              ->with($productIdsCollection)
              ->once()
              ->andReturn($productCollection);
        });

        $productCollection = app(ProductService::class)->getProductsByIds($productIdsCollection);

        $this->assertInstanceOf(ProductCollection::class, $productCollection);
    }
}
