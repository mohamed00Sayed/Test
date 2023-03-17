<?php

declare(strict_types=1);

namespace Moham\Test\Repository;

class RepositoryFactory
{
    private $repos;

    public function __construct($repos)
    {
        if (!is_array($repos)) {
            throw new \RuntimeException('$repos must be an associative array');
        }

        foreach ($repos as $key => $class) {
            if (!is_subclass_of($class, Repository::class)) {
                throw new \RuntimeException("RepositoryFactory is only for Repository subclasses");
            }
        }

        $this->repos = $repos;
    }

    public function getRepository(string $type): Repository
    {
        return new $this->repos[$type]();
    }
}
