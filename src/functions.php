<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;

function getErrorCode(int|Throwable $code): int
{
    if ($code instanceof Throwable) $code = $code->getCode();
    return $code >= 400 && $code < 600 ? $code : 500 ;
}

function createExceptionHandlerCallback(): callable
{
    return static function(int $errno, string $msg, string $file, int $line): void {
        if ((error_reporting() & $errno)) {
            throw new \ErrorException($msg, 0, $errno, $file, $line);
        }
    };
}

function createEvent(Throwable $e): ErrorEvent
{
    return new ErrorEvent($e, $e?->serverRequest ?? null);
}

function createListener(callable $callable, int $priority = 1): ErrorListenerInterface
{
    return new class($callable, $priority = 1) implements ErrorListenerInterface {
        public function __construct(private $callable, public readonly int $priority) {
        }
        public function handleEvent(ErrorEvent $event): void 
        {
            ($this->callable)($event);
        }
    };
}
