<?php

namespace Bermuda\ErrorHandler;

use Psr\Http\Server\RequestHandlerInterface;

/**
 * Interface ErrorHandlerInterface
 * @package Bermuda\ErrorHandler
 */
interface ServerRequestAwareInterface
{
    /**
     * @return ServerRequestInterface
     */
    public function getServerRequest(): ServerRequestInterface ;
}
