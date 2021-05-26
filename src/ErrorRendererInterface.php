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
     * @return string
     */
    public function renderException(Throwable $e): string ;
}
