<?php

namespace Bermuda\ErrorHandler;

use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;

/**
 * Class ErrorHandlerMiddleware
 * @package Bermuda\ErrorHandler
 */
final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private EventDispatcherInterface $dispatcher;
    private ?PrioritizedProvider $provider = null;
    private ErrorResponseGeneratorInterface $generator;

    public function __construct(ErrorResponseGeneratorInterface $generator, EventDispatcherInterface $dispatcher = null)
    {
        $this->setGenerator($generator);
        $this->setDispatcher($dispatcher ?? new EventDispatcher());
    }
    
    /**
     * @param int $level
     * @return int
     */
    public function setErrorLevel(int $level): int 
    {
        return error_reporting($level);
    }
    
    /**
     * @param EventDispatcherInterface $dispatcher
     * @return self
     */
    public function setDispatcher(?EventDispatcherInterface $dispatcher): self
    {
        if (!$this->provider)
        {
            $this->provider = new PrioritizedProvider();
        }
        
        $this->dispatcher = $dispatcher->attach($this->provider);
        
        return $this;
    }
    
    /**
     * @param ErrorResponseGenerator $generator
     * @return self
     */
    public function setGenerator(ErrorResponseGeneratorInterface $generator): self
    {
        $this->generator = $generator;
        return $this;
    }
    
    /**
     * @param ErrorListenerInterface $listener
     * @return void
     */
    public function listen(ErrorListenerInterface $listener, int $priority = 0): void
    {
        $this->provider->listen($listener, $priority);
    }
    
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler($this->createHandler());

        try
        {
           $response = $handler->handle($request);
        }

        catch (\Throwable $e)
        {
            $response = $this->generator->generate($e, $request);
            
            if ($this->dispatcher)
            {
                $response = $this->dispatcher
                    ->dispatch(new ErrorEvent($e, $request, $response))
                    ->response();
            }
        }
        
        restore_error_handler();

        return $response;
    }
    
    private function createHandler(): callable
    {
        return new class
        {
            public function __invoke(int $errno, string $msg, string $file, int $line)
            {
                if (!(error_reporting() & $errno))
                {
                    return;
                }

                throw new \ErrorException($msg, 0, $errno, $file, $line);
            }
        };
    }
}
