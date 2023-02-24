<?php

declare(strict_types=1);

namespace Moham\Test\Main;

class Dvd extends Product
{
    public int $size;

    public function __construct(string $sku, string $name, float $price, int $size)
    {
        parent::__construct($sku, $name, $price);
        $this->size = $size;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'size' => $this->size
        ];
    }
}
