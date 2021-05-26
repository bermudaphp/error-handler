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
     * @param ErrorEvent $event
     */
    public function handleException(Throwable $e, ?array $context = null): void ;
}
