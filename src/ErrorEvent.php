<?php

namespace Bermuda\ErrorHandler;

use Throwable;

class ErrorEvent
{
    protected Throwable $e;
   
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
