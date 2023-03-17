<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

use Moham\Test\Main\Product;
use stdClass;

/**
 * a product buider knows how to build a specific product type
 */
interface ProductBuilder
{
    public function getProductInstance(stdClass $stdInstance): Product;
}
