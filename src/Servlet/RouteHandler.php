<?php

declare(strict_types=1);

namespace Moham\Test\Servlet;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class RouteHandler
{
    public const GET = "GET";
    public const POST = "POST";
    public const DELETE = "DELETE";

    abstract public function getPath(): string;
    abstract public function getMethod(): string;
    abstract public function execute($request): ResponseInterface;

    protected function createResponse(int $status, array $data): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();
        $responseBody = $psr17Factory->createStream(json_encode($data));
        $response = $psr17Factory->createResponse($status)->withBody($responseBody);
        return $response;
    }
}
