<?php

namespace Bermuda\ErrorHandler;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * @param Throwable $e
 * @return int
 */
function get_status_code_from_throwable(Throwable $e): int
{
    return get_error_code($e->getCode());
}

function get_error_code(int|Throwable $code): int
{
    if ($code instanceof Throwable) {
        $code = $code->getCode();
    }

    return $code >= 400 && $code < 600 ? $code : 500 ;
}

function createEvent(Throwable $e, ?ServerRequestInterface $serverRequest = null): ErrorEvent|ServerErrorEvent
{
    if ($e instanceof ServerException) {
        return new ServerErrorEvent($e->getPrevious(), $e->getServerRequest());
    } elseif ($serverRequest != null) {
        return new ServerErrorEvent($e, $serverRequest);
    }
    
    return new ErrorEvent($e);
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
