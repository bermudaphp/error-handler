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
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
