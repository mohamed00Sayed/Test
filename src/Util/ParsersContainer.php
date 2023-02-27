<?php

declare(strict_types=1);

namespace Moham\Test\Util;

use Psr\Container\ContainerInterface;
use Yiisoft\Request\Body\Parser\JsonParser;

class ParsersContainer implements ContainerInterface
{
    private array $parsers = [];

    public function __construct()
    {
        $this->parsers = [JsonParser::class => new JsonParser()];
    }

    public function get(string $id)
    {
        return $this->parsers[$id];
    }

    public function has(string $id): bool
    {
        foreach ($this->parsers as $parserClass => $parser) {
            if (strcmp($parserClass, $id) === 0) return true;
        }
        return false;
    }
}
