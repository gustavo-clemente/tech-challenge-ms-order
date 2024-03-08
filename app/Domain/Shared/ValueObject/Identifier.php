<?php

declare(strict_types= 1);

namespace App\Domain\Shared\ValueObject;

abstract class Identifier implements \JsonSerializable
{
    public function __construct(
        private int|string $id
    ){

    }

    public function getIdentifier(): int|string
    {
        return $this->id;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id
        ];
    }
}
