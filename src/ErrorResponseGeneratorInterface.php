<?php


namespace Bermuda\ErrorHandler;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Interface ErrorResponseGeneratorInterface
 * @package Bermuda\ErrorHandler
 */
interface ErrorResponseGeneratorInterface
{
    public function generate(\Throwable $e, ServerRequestInterface $request): ResponseInterface;
}
