<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorResponseGeneratorInterface
{
    /**
     * @param Throwable $e
     * @return bool
     * returns true if the generator can handle the exception, otherwise returns false
     */
    public function canGenerate(Throwable $e): bool ;

    /**
     * @param Throwable $e
     * @return ResponseInterface
     * @throws Throwable if self::canGenerate method return false
     */
    public function generateResponse(Throwable $e): ResponseInterface ;
}

