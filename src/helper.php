<?php

namespace Bermuda\ErrorHandler;
use Throwable;

/**
 * @param Throwable $e
 * @return int
 */
function get_status_code_from_throwable(Throwable $e): int
{
    return get_error_code($e->getCode());
}

function get_error_code(int $code): int
{
    return $code >= 400 && $code < 600 ? $code : 500 ;
}

function createEvent(Throwable $e): ErrorEvent|ServerErrorEvent
{
    return $e instanceof ServerException ? new ServerErrorEvent($e->getPrevious(), $e->getServerRequest()) : new ErrorEvent($e);
}

function createListener(callable $callable, int $priority): ErrorListenerInterface
{
    return new class($callable, $priority = 1) implements ErrorListenerInterface {
        use ErrorListener;
        public function __construct(private $callable, int $priority)
        {
            $this->priority = $priority;
        }
        public function handleEvent(ErrorEvent $event): void 
        {
            ($this->callable)($event);
        }
    };
}
