<?php

namespace Bermuda\ErrorHandler;

use Throwable;

interface ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     */
    public function handleException(Throwable $e): void ;
}
