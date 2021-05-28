<?php

namespace Bermuda\ErrorHandler;

use Throwable;

/**
 * Class ErrorEvent
 * @package Bermuda\ErrorHandler
 */
class ErrorEvent
{
    private Throwable $e;
   
    public function __construct(Throwable $e)
    {
        $this->e = $e;
    }

    /**
     * @return Throwable
     */
    public function getThrowable(): Throwable
    {
        return $this->e;
    }
}
