<?php

namespace Bermuda\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestHandlingException
 * @package Bermuda\ErrorHandler
 */
final class RequestHandlingException extends \RuntimeException
{
    private ServerRequestInterface $serverRequest;
    
    public function __construct(\Throwable $e, ServerRequestInterface $request)
    {
        $this->serverRequest = $request;
        parent::__construct($e->getMessage(), $this->getStatusCode($e), $e);
        $this->file = $e->getFile(); $this->line = $e->getLine();
    }
    
    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }
    
    public static function getStatusCode(\Throwable $e): int
    {
        return $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500 ;
    }
}
