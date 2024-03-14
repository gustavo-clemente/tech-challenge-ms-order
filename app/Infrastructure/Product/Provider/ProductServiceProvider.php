<?php

declare(strict_types=1);

namespace App\Infrastructure\Product\Provider;

use App\Domain\Product\Port\MsAdapter\ProductMsAdapter;
use App\Infrastructure\Product\Adapter\MsAdapter\MockProductMsAdapter;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    public $bindings = [
        ProductMsAdapter::class => MockProductMsAdapter::class
       ];
}
