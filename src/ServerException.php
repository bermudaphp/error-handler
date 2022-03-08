<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

final class ServerException extends RuntimeException
{    
    public function __construct(public readonly Throwable $throwable, public readonly ServerRequestInterface $serverRequest)
    {
        parent::__construct('ServerException', get_error_code($throwable), $throwable);
    }
    
    public static function getStatusCode(Throwable $throwable): int
    {
        return get_error_code($throwable->getCode());
    }
}
