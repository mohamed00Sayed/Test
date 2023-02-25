<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

use stdClass;
use Moham\Test\Main\Dvd;

class DvdBuilder implements ProductBuilder
{
    public function getProductInstance(stdClass $std): Dvd
    {
        return new Dvd($std->sku, $std->name, $std->price, $std->size);
    }
}
