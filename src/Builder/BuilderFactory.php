<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

class BuilderFactory
{
    protected $BUILDERS;
    public const BOOK = 'book';
    public const DVD = 'dvd';
    public const FURNITURE = 'furniture';

    public function __construct()
    {
        $this->BUILDERS = [
            BuilderFactory::BOOK => new BookBuilder(),
            BuilderFactory::DVD => new DvdBuilder(),
            BuilderFactory::FURNITURE => new FurnitureBuilder()
        ];
    }

    public  function getBuilder(string $type): ProductBuilder
    {
        return $this->BUILDERS[$type];
    }
}
