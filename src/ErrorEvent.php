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
    private ?ResponseInterface $response = null;
    private ?ServerRequestInterface $request = null;

    public function __construct(Throwable $e, ?ServerRequestInterface $request = null, ?ResponseInterface $response = null)
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
     * @return ResponseInterface|null
     */
    public function response(?ResponseInterface $response = null):? ResponseInterface
    {
        return $response != null ? $this->response = $response : $this->response;
    }

    /**
     * @param ServerRequestInterface|null $request
     * @return ServerRequestInterface|null
     */
    public function request(?ServerRequestInterface $request = null):? ServerRequestInterface
    {
        return $request != null ? $this->request = $request : $this->request;
    }
}
