<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class HttpErrorEvent
 * @package Bermuda\ErrorHandler
 */
final class HttpErrorEvent extends ErrorEvent
{
    private ResponseInterface $response;
    private ServerRequestInterface $request;

    public function __construct(HttpException $e, ResponseInterface $response)
    {
        $this->request = $e->getServerRequest();
        parent::__construct($e); $this->response = $response;
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
    
    /**
     * @return HttpException
     */
    public function getThrowable(): HttpException
    {
        return $this->e;
    }
}
