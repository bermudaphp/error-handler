<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class ServerErrorEvent extends ErrorEvent
{
    public function __construct(Throwable $e, private ServerRequestInterface $request, private ResponseInterface $response)
    {
        parent::__construct($e->getPrevious());
    }
     
    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
    
    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }
    
    public function setRequest(ServerRequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }
    
    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
