<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

class BuilderFactory
{
    private $BUILDERS;

    public function __construct()
    {
        $this->BUILDERS = [
            'book' => new BookBuilder(),
            'dvd' => new DvdBuilder(),
            'furniture' => new FurnitureBuilder()
        ];
    }

    public  function getBuilder(string $type): ProductBuilder
    {
        return $this->BUILDERS[$type];
    }
}
