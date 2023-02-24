<?php

namespace Moham\Test\Servlet;

use Slim\Psr7\Factory\ServerRequestFactory;

class HttpServlet implements Servlet
{
    private $request;
    private $handled;

    public function __construct()
    {
        $this->request = ServerRequestFactory::createFromGlobals();
        $this->handled = false;
    }

    public function get($route, $callback)
    {
        $this->handle($route, $callback, 'GET');
    }

    public function post($route, $callback)
    {
        $this->handle($route, $callback, 'POST');
    }

    public function delete($route, $callback)
    {
        $this->handle($route, $callback, 'DELETE');
    }

    private function handle($route, $callback, $method)
    {
        if ($this->handled == true) {
            return;
        }

        if (strcasecmp($this->request->getMethod(), $method) !== 0) {
            return;
        }

        $path = $this->request->getUri()->getPath();

        if (strcmp($route, $path) == 0) {

            $callback($this->request);
            $this->handled = true;
        }
    }
}
