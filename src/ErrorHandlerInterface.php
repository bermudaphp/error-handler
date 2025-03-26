<?php

namespace Bermuda\ErrorHandler;

interface ErrorHandlerInterface extends ExceptionHandlerInterface
{
    public function canHandle(\Throwable $e): bool ;
    public function listen(Listener\ErrorListenerInterface $listener): ErrorHandlerInterface ;
}
