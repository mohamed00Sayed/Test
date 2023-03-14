<?php

declare(strict_types=1);

namespace Moham\Test\Server;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class StationResolver implements RequestHandlerInterface
{
    private array $stations;

    public function __construct(array $stations)
    {
        $this->stations = $stations;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $station = current($this->stations);
        next($this->stations);

        if ($station instanceof MiddlewareInterface) {
            return $station->process($request, $this);
        }

        if ($station instanceof RequestHandlerInterface) {
            return $station->handle($request);
        }

        throw new \RuntimeException(
            sprintf(
                'Invalid station: %s. Station must either be %s or %s.',
                $station,
                RequestHandlerInterface::class,
                MiddlewareInterface::class
            )
        );
    }
}
