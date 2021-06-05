<?php

namespace Bermuda\ErrorHandler;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface ErrorResponseGeneratorInterface
 * @package Bermuda\ErrorHandler
 */
interface ErrorResponseGeneratorInterface
{
    public function generate(HttpException $e): ResponseInterface;
}
