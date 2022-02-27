<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;

final class ErrorHandlerMiddleware implements MiddlewareInterface, EventDispatcherAwareInterface
{
    private PrioritizedProvider $provider;
    public function __construct(private Generator\ErrorResponseGenerator $generator, 
        private EventDispatcherInterface $dispatcher = new ErrorDispatcher, private int $errorLevel = E_ALL
    ) {
        $this->provider = new PrioritizedProvider;
        $this->dispatcher = $dispatcher->attach($this->provider);
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
            $response = $this->generator->generateResponse($e, $request);
            $this->dispatcher->dispatch(new ServerErrorEvent($e, $request));
        }
        
        restore_error_handler();
        error_reporting($old);
        
        return $response;
    }
    
    /**
     * @param EventDispatcherInterface $dispatcher
     * @return self
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): EventDispatcherAwareInterface
    {
        if ($this->provider == null) {
            $this->provider = new PrioritizedProvider;
        }

        $this->dispatcher = $dispatcher->attach($this->provider);
        return $this;
    }

    /**
     * @param ErrorListenerInterface $listener
     * @return static
     */
    public function on(ErrorListenerInterface $listener): self
    {
        $this->provider->listen(ServerErrorEvent::class, $listener, $listener->getPriority());
        return $this;
    }
}
