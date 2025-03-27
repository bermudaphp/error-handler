<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class ErrorEvent
{
    public function __construct(
        public readonly Throwable $exception,
        public readonly ?ServerRequestInterface $serverRequest = null,
        public ?ResponseInterface $response = null
    )  {}
}
