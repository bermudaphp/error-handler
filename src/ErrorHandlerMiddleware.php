<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\EventDispatcherAwareInterface;

final class ErrorHandlerMiddleware implements MiddlewareInterface, EventDispatcherAwareInterface
{
    public function __construct(private ErrorHandler $errorHandler, private int $errorLevel = E_ALL
    ) {
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return self
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): EventDispatcherAwareInterface
    {
        $this->errorHandler->setDispatcher($dispatcher);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $old = error_reporting($this->errorLevel);
        set_error_handler(static function(int $errno, string $msg, string $file, int $line): void {
            if ((error_reporting() & $errno)) {
                throw new \ErrorException($msg, 0, $errno, $file, $line);
            }
        });

        try {
            $response = $handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->errorHandler->generateResponse($e, $request);
        }
        
        restore_error_handler();
        error_reporting($old);
        
        return $response;
    }

    /**
     * @param ErrorListenerInterface $listener
     * @return static
     */
    public function on(ErrorListenerInterface $listener): self
    {
        $this->errorHandler->on($listener);
        return $this;
    }
}
