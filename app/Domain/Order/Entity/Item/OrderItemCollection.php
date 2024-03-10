<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity\Item;

use App\Domain\Shared\Collection;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Exception\Item\DuplicateProductItemException;

/** @method OrderItem current() */
class OrderItemCollection extends Collection implements \JsonSerializable
{
    public function __construct(array $data){
        parent::__construct($data);

        $this->validate();
    }
  
    private function validate(): void
    {
        $productIds = [];
        
        foreach($this as $orderItem){
            $itemProductIdentifier = $orderItem->getProductId()->getIdentifier();

            if(in_array($itemProductIdentifier, $productIds)){
                throw new DuplicateProductItemException(
                    "Two products with the same ID were found in the collection.
                    Each product must have a unique identifier"
                );
            }

            $productIds[] = $itemProductIdentifier;
        }
    }
    public function jsonSerialize(): array
    {
        $items = [];

        foreach($this as $item){
            $items[] = $item->jsonSerialize();
        }

        return $items;
    }
}
