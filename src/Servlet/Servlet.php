<?php

namespace Moham\Test\Servlet;

use Psr\Http\Server\RequestHandlerInterface;

interface Servlet extends RequestHandlerInterface
{
    public function get(string $path, callable $callback);
    public function post(string $path, callable $callback);
    public function delete(string $path, callable $callback);
}
