<?php


namespace Lobster;


use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Class ErrorEvent
 * @package Lobster
 */
class ErrorEvent implements Contracts\ErrorEvent
{
    protected Throwable $error;
    protected ResponseInterface $response;
    protected ServerRequestInterface $request;

    /**
     * ErrorEvent constructor.
     * @param Throwable $e
     * @param ServerRequestInterface $req
     * @param ResponseInterface $resp
     */
    public function __construct(Throwable $e, ServerRequestInterface $req, ResponseInterface $resp)
    {
        $this->error = $e;
        $this->request = $req;
        $this->response = $resp;
    }

    /**
     * @inheritDoc
     */
    public function getError(): \Throwable
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
