<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorResponseGeneratorInterface
{
    /**
     * @param Throwable $e
     * @param ServerRequestInterface|null $request
     * @return bool
     * returns true if the generator can handle the exception, otherwise returns false
     */
    public function canGenerate(Throwable $e, ServerRequestInterface $request = null): bool ;

    /**
     * @param Throwable $e
     * @param ServerRequestInterface|null $request
     * @return ResponseInterface
     * @throws Throwable if self::canGenerate method return false
     */
    public function generateResponse(Throwable $e, ServerRequestInterface $request = null): ResponseInterface ;
}

