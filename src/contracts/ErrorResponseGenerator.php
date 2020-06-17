<?php


namespace Lobster\Contracts;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Interface ErrorResponseGenerator
 * @package Lobster\Contracts
 */
interface ErrorResponseGenerator
{
    public function generate(\Throwable $e, ServerRequestInterface $request): ResponseInterface;
}
