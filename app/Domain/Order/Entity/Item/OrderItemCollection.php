<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity\Item;

use App\Domain\Shared\Collection;
use App\Domain\Order\Entity\Item\OrderItem;

/** @method OrderItem current() */
class OrderItemCollection extends Collection
{
    public function __construct(array $data){
        parent::__construct($data);
    }
    private function validate(): void
    {
        
    }
}
