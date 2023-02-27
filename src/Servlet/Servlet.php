<?php

namespace Moham\Test\Servlet;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface Servlet extends RequestHandlerInterface
{
    public function dispatch(ServerRequestInterface $request): ResponseInterface;
}
