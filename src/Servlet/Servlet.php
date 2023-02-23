<?php

namespace Moham\Test\Servlet;

interface Servlet
{
    public function get($app_route, $callback);
    public function post($app_route, $callback);
    public function delete($app_route, $callback);
}