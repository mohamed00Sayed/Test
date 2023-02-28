<?php

declare(strict_types=1);

namespace Moham\Test\Repository;

class RepositoryFactory
{
    protected $REPOS;
    public const BOOK = 'book';
    public const DVD = 'dvd';
    public const FURNITURE = 'furniture';

    public function __construct()
    {
        $this->REPOS = [
            RepositoryFactory::BOOK => new BookRepository(),
            RepositoryFactory::DVD => new DvdRepository(),
            RepositoryFactory::FURNITURE => new FurnitureRepository()
        ];
    }

    public  function getRepository(string $type): Repository
    {
        return $this->REPOS[$type];
    }
}
