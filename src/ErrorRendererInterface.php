<?php

namespace Bermuda\ErrorHandler;

use \Throwable;

/**
 * Interface ErrorRendererInterface
 * @package Bermuda\ErrorHandler
 */
interface ErrorRendererInterface
{
    /**
     * @param Throwable $e
     * @return string
     */
    public function renderException(Throwable $e): string ;
}
