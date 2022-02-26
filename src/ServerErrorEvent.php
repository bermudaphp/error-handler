<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class ServerErrorEvent extends ErrorEvent
{
    public function __construct(Throwable $e, private ServerRequestInterface $request)
    {
        parent::__construct($e);
    }
     
    /**
     * @return ServerRequestInterface
     */
    public function getServerRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
