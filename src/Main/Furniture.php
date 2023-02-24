<?php

declare(strict_types=1);

namespace Moham\Test\Main;

class Furniture extends Product
{
    public Dimension $dimensions;

    public function __construct(string $sku, string $name, float $price, Dimension $dimensions)
    {
        parent::__construct($sku, $name, $price);
        $this->dimensions = $dimensions;
    }

    public function getDimensions(): Dimension
    {
        return $this->dimensions;
    }

    public function setDimensions(Dimension $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'dimesions' => $this->dimensions
        ];
    }
}
