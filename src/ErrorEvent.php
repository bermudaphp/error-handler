<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ErrorEvent
 * @package Bermuda\ErrorHandler
 */
final class ErrorEvent extends \RuntimeException
{
    private ResponseInterface $response;
    private ServerRequestInterface $request;

    public function __construct(Throwable $e, ServerRequestInterface $req, ResponseInterface $resp)
    {
        parent::__construct($e->getMessage(), $e->getCode(), $e);
        
        $this->file = $e->getFile();
        $this->line = $e->getLine();
        
        $this->request = $req;
        $this->response = $resp;
    }

    /**
     * @return Throwable
     */
    public function getThrowable(): Throwable
    {
        return $this->getPrevious();
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
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
