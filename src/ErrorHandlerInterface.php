<?php

namespace Bermuda\ErrorHandler;

use Throwable;

interface ErrorHandlerInterface extends ErrorRendererInterface
{
    public function canHandle(Throwable $e): bool ;
    public function handleException(Throwable $e): never ;
}
