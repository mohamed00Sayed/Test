<?php

declare(strict_types=1);

namespace Moham\Test\Servlet;

use Moham\Test\Server\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpServlet implements Servlet
{
    private array $handlers = [];

    public function get(string $path, callable $callback)
    {
        array_push($this->handlers, new RouteHandler($path, RouteHandler::GET, $callback));
    }

    public function post(string $path, callable $callback)
    {
        array_push($this->handlers, new RouteHandler($path, RouteHandler::POST, $callback));
    }

    public function delete(string $path, callable $callback)
    {
        array_push($this->handlers, new RouteHandler($path, RouteHandler::DELETE, $callback));
    }

    public function options(string $path, callable $callback)
    {
        array_push($this->handlers, new RouteHandler($path, RouteHandler::OPTIONS, $callback));
    }

    private function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        foreach ($this->handlers as $handler) {
            /*excute the correct handler based on the path and the method*/
            if (strcasecmp($handler->getMethod(), $method) == 0 && strcasecmp($handler->getPath(), $path) == 0) {
                return $handler->getCallback()($request);
            }
        }
        /*if no handler is found, then it's a NOT FOUND case*/
        return new Response(404);
    }

    public function handle($request): ResponseInterface
    {
        return $this->dispatch($request);
    }
}
