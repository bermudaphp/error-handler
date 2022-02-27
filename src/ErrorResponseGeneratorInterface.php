<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorResponseGeneratorInterface
{
    public function canGenerate(Throwable $e, ServerRequestInterface $request = null): bool ;
    public function generateResponse(Throwable $e, ServerRequestInterface $request = null): ResponseInterface ;
}
