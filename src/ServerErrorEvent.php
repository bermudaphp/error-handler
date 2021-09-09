<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ServerErrorEvent extends ErrorEvent
{
    private ResponseInterface $response;
    private ServerRequestInterface $request;

    public function __construct(ServerException $e, ResponseInterface $response)
    {
        $this->request = $e->getServerRequest();
        parent::__construct($e->getPrevious()); $this->response = $response;
    }
    
    /**
     * @param ResponseInterface|null $response
     * @return ResponseInterface
     */
    public function response(?ResponseInterface $response = null): ResponseInterface
    {
        return $response != null ? $this->response = $response : $this->response;
    }

    /**
     * @param ServerRequestInterface|null $request
     * @return ServerRequestInterface|null
     */
    public function request(ServerRequestInterface $request = null): ServerRequestInterface
    {
        return $request != null ? $this->request = $request : $this->request;
    }
}
