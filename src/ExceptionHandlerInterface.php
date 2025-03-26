<?php

namespace Bermuda\ErrorHandler;

interface ExceptionHandlerInterface
{
    public function handleException(\Throwable $e): never ;
}
