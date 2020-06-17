<?php


namespace Lobster\Contracts;


use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Interface ErrorEvent
 * @package Lobster\Contracts
 */
interface ErrorEvent
{
    /**
     * @return Throwable
     */
    public function getError(): Throwable;

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface;
}
