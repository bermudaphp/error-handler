<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ErrorEvent
 * @package Bermuda\ErrorHandler
 */
final class ErrorEvent
{
    private Throwable $e;
    private ResponseInterface $response;
    private ServerRequestInterface $request;

    public function __construct(Throwable $e, ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->e = $e;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return Throwable
     */
    public function getThrowable(): Throwable
    {
        return $this->e;
    }

    /**
     * @param ResponseInterface|null $response
     * @return ResponseInterface
     */
    public function response(?ResponseInterface $response = null): ResponseInterface
    {
        if ($response != null)
        {
            $this->response = $response;
        }
        
        return $this->response;
    }

    /**
     * @param ServerRequestInterface|null $request
     * @return ServerRequestInterface
     */
    public function request(?ServerRequestInterface $request = null): ServerRequestInterface
    {
        if ($request != null)
        {
            $this->request = $request;
        }
        
        return $this->request;
    }
}
