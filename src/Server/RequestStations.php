<?php

declare(strict_types=1);

namespace Moham\Test\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestStations implements RequestHandlerInterface
{
    private array $stations;

    public function __construct(array $stations)
    {
        $this->stations = $stations;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /*set the internal pointer to the first element in the array*/
        reset($this->stations);
        $stationResolver = new StationResolver($this->stations);
        return $stationResolver->handle($request);
    }
}
