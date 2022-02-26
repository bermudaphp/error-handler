<?php

namespace Bermuda\ErrorHandler;

use Throwable;

class ErrorEvent
{
    public readonly Throwable $throwable;
   
    public function __construct(Throwable $e)
    {
        $this->throwable = $e;
    }

    /**
     * @return Throwable
     */
    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}
