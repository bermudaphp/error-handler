<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;

class ErrorEvent
{
    public function __construct(public readonly Throwable $throwable, 
        public readonly ?ServerRequestInterface $serverRequest = null) {
    }
}
