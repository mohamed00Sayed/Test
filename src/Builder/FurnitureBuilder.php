<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

use Moham\Test\Main\Dimension;
use stdClass;
use Moham\Test\Main\Furniture;

class FurnitureBuilder implements ProductBuilder
{
    public function getProductInstance(stdClass $std): Furniture
    {
        return new Furniture(
            $std->sku,
            $std->name,
            $std->price,
            new Dimension($std->dimensions->height, $std->dimensions->width, $std->dimensions->length)
        );
    }
}
