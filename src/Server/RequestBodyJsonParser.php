<?php

declare(strict_types=1);

namespace Moham\Test\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestBodyJsonParser implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $parsed = $this->parse((string)$request->getBody());
            if ($parsed !== null && !is_object($parsed) && !is_array($parsed)) {
                throw new \RuntimeException(
                    "RequestBodyJsonParser Exception: invalid JSON"
                );
            }
            $request = $request->withParsedBody($parsed);
        } catch (\JsonException $ex) {
            $response = new Response(400);
            $response->getBody()->write('Bad Request ' . $ex->getMessage());
            return $response;
        }

        return $handler->handle($request);
    }

    private function parse(string $rawBody)
    {
        if ($rawBody === '') {
            return null;
        }

        $result = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE);
        if (is_array($result) || is_object($result)) {
            return $result;
        }

        return null;
    }
}
