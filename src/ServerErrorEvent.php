<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class ServerErrorEvent extends ErrorEvent
{
    public function __construct(Throwable $e, public readonly ServerRequestInterface $serverRequest)
    {
        parent::__construct($e);
    }
     
    /**
     * @return ServerRequestInterface
     */
    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }
}
