<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestException extends RuntimeException
{
    private ServerRequestInterface $serverRequest;
    
    public function __construct(Throwable $e, ServerRequestInterface $request)
    {
        $this->serverRequest = $request;
        parent::__construct($e->getMessage(), $this->getStatusCode($e), $e);
    }
    
    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }
    
    public static function getStatusCode(Throwable $e): int
    {
        return $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500 ;
    }
}
