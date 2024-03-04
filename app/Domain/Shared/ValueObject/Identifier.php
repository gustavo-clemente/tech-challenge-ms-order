<?php

declare(strict_types= 1);

namespace App\Domain\Shared\ValueObject;

abstract class Identifier implements \JsonSerializable
{
    public function __construct(
        private string $id
    ){

    }

    public function getIdentifer(): string
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
