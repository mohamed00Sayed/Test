<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

use stdClass;
use Moham\Test\Main\Book;


class BookBuilder implements ProductBuilder
{
    public function getProductInstance(stdClass $std): Book
    {
        return new Book($std->sku, $std->name, $std->price, $std->weight);
    }
}
