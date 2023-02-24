<?php

declare(strict_types=1);

namespace Moham\Test\Main;

class Book extends Product
{
    private float $weight;

    public function __construct(string $sku, string $name, float $price, float $weight)
    {
        parent::__construct($sku, $name, $price);
        $this->weight = $weight;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'weight' => $this->weight
        ];
    }
}
