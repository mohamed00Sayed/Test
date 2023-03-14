<?php

declare(strict_types=1);

namespace Moham\Test\Server;

use Psr\Http\Message\ResponseInterface;

/**
 * Implementation from Laminas\HttpHandlerRunner\Emitter
 */
class SapiEmitter
{
    public function emit(ResponseInterface $response): bool
    {
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $this->emitBody($response);

        return true;
    }

    private function emitBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }

    private function emitStatusLine(ResponseInterface $response): void
    {
        $reasonPhrase = $response->getReasonPhrase();
        $statusCode   = $response->getStatusCode();

        $this->header(sprintf(
            'HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $statusCode,
            $reasonPhrase ? ' ' . $reasonPhrase : ''
        ), true, $statusCode);
    }

    private function emitHeaders(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        foreach ($response->getHeaders() as $header => $values) {
            assert(is_string($header));
            $name  = $this->filterHeader($header);
            $first = $name !== 'Set-Cookie';
            foreach ($values as $value) {
                $this->header(sprintf(
                    '%s: %s',
                    $name,
                    $value
                ), $first, $statusCode);
                $first = false;
            }
        }
    }

    private function filterHeader(string $header): string
    {
        return ucwords($header, '-');
    }


    private function header(string $headerName, bool $replace, int $statusCode): void
    {
        header($headerName, $replace, $statusCode);
    }
}
