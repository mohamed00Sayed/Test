<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

class ProductBuilderFactory
{
    protected $BUILDERS;
    public const BOOK = 'book';
    public const DVD = 'dvd';
    public const FURNITURE = 'furniture';

    public function __construct()
    {
        $this->BUILDERS = [
            ProductBuilderFactory::BOOK => new BookBuilder(),
            ProductBuilderFactory::DVD => new DvdBuilder(),
            ProductBuilderFactory::FURNITURE => new FurnitureBuilder()
        ];
    }

    public  function getBuilder(string $type): ProductBuilder
    {
        return $this->BUILDERS[$type];
    }
}
