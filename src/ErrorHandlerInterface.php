<?php

namespace Bermuda\ErrorHandler;

use Throwable;

interface ErrorHandlerInterface
{
    public function canHandle(Throwable $e): bool ;
    public function handleException(Throwable $e): never ;
    public function on(ErrorListenerInterface $listener): ErrorHandlerInterface ;
}
