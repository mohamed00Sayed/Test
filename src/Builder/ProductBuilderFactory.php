<?php

declare(strict_types=1);

namespace Moham\Test\Builder;

class ProductBuilderFactory
{
    private $builders;

    public function __construct($builders)
    {
        if (!is_array($builders)) {
            throw new \RuntimeException('$builders must be an associative array');
        }

        foreach ($builders as $key => $class) {
            if (!is_subclass_of($class, ProductBuilder::class)) {
                throw new \RuntimeException("ProductBuilderFactory is only for ProductBuilder subclasses");
            }
        }

        $this->builders = $builders;
    }

    public function getBuilder(string $type): ProductBuilder
    {
        return new $this->builders[$type]();
    }
}
