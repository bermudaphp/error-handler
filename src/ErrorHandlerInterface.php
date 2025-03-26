<?php

namespace Bermuda\ErrorHandler;

use Throwable;

interface ErrorHandlerInterface extends ExceptionHandlerInterface
{
    public function canHandle(Throwable $e): bool ;
    public function listen(ErrorListenerInterface $listener): ErrorHandlerInterface ;
}
