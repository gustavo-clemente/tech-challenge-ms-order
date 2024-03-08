<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use Countable;
use Iterator;

abstract class Collection implements Iterator, Countable
{
    private $position = 0;
    public function __construct(
        private array $data
    ){
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): mixed
    {
        return $this->data[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }

    public function count(): int
    {
        return count($this->data);
    }
    
}
