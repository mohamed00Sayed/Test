<?php

declare(strict_types=1);

namespace Moham\Test\Exceptions;

use LogicException;

class MissingArgumentException extends LogicException
{
    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
