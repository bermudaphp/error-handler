<?php

namespace Bermuda\ErrorHandler;

use Throwable;

interface ErrorHandlerInterface extends ErrorRendererInterface
{
    /**
     * @param Throwable $e
     */
    public function handleException(Throwable $e): never ;
}
