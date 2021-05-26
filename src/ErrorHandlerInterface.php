<?php

namespace Bermuda\ErrorHandler;

use \Throwable;

/**
 * Interface ErrorHandlerInterface
 * @package Bermuda\ErrorHandler
 */
interface ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     */
    public function handleException(Throwable $e): void ;
}
