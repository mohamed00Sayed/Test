<?php

declare(strict_types=1);

namespace Moham\Test\Servlet;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class RouteHandler
{
    public const GET = "GET";
    public const POST = "POST";
    public const DELETE = "DELETE";

    private string $route;
    private string $method;
    private $callback;

    public function __construct(string $route, string $method, callable $callback)
    {
        $this->route = $route;
        $this->method = $method;
        $this->callback = $callback;
    }

    public function getPath(): string
    {
        return $this->route;
    }
    public function getMethod(): string
    {
        return $this->method;
    }
    public function getCallback(): callable
    {
        return $this->callback;
    }
}
