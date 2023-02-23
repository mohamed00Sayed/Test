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
        if ($this->handled == true) {
            return;
        }

        if (strcasecmp($this->request->getMethod(), 'GET') !== 0) {
            return;
        }

        $path = $this->request->getUri()->getPath();
        $vars = array();

        //if there is a match between the request and the handler route
        if ($this->doesMatch($route, $path, $vars)) {
            foreach ($vars as $k => $v) {
                $this->request = $this->request->withAttribute($k, $v);
            }
            $callback($this->request);
            $this->handled = true;
        }
    }

    public function post($route, $callback)
    {
        if ($this->handled == true) {
            return;
        }

        if (strcasecmp($this->request->getMethod(), 'POST') !== 0) {
            return;
        }

        $path = $this->request->getUri()->getPath();

        if ($this->doesMatch($route, $path, $vars)) {
            $callback($this->request);
            $this->handled = true;
        }
    }

    public function delete($route, $callback)
    {
        if ($this->handled == true) {
            return;
        }

        if (strcasecmp($this->request->getMethod(), 'DELETE') !== 0) {
            return;
        }

        $path = $this->request->getUri()->getPath();

        if ($this->doesMatch($route, $path, $vars)) {

            $callback($this->request);
            $this->handled = true;
        }
    }

    private function doesMatch($route, $path, &$vars): bool
    {
        if (str_contains($route, ":") == false) {
            //if there is no query params, then paths must be exact match
            //gets here when mehtod is [post, delete].
            return strcmp($route, $path) == 0 ? true : false;
        } else {
            //enters here when method is [get] with specific path variable.
            return $this->extractPathVars($route, $path, $vars);
        }
    }

    private function extractPathVars($route, $url, &$arr): bool
    {
        $route_tokens = explode('/', $route);
        $url_tokens = explode('/', $url);


        if (count($route_tokens) != count($url_tokens)) {
            return false;
        }

        for ($x = 0; $x < count($url_tokens); $x++) {
            $R = $route_tokens[$x];
            $U = $url_tokens[$x];

            if (str_contains($R, ":") == true) {
                $arr[substr($R, 1)] = $U;
                continue;
            }

            if (strcmp($R, $U) !== 0) return false;
        }

        return true;
    }
}
