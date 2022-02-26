<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

final class ServerException extends RuntimeException
{    
    public function __construct(Throwable $e, private ServerRequestInterface $request)
    {
        parent::__construct('ServerException', get_error_code($e->getCode()), $e);
    }
    
    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }
    
    public static function getStatusCode(Throwable $e): int
    {
        return get_error_code($e->getCode());
    }
}
