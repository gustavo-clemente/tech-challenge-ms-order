<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity\Item;
use App\Domain\Shared\Collection;
use App\Domain\Order\ValueObject\Item\OrderItemId;

/** @method OrderItemId current() */
class OrderItemIdCollection extends Collection
{
}
