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
use Bermuda\RequestHandlerRunner\ServerRequestFactory;

/**
 * Class ErrorHandlerMiddleware
 * @package Bermuda\ErrorHandler
 */
final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private int $errorLevel;
    private EventDispatcherInterface $dispatcher;
    private ?PrioritizedProvider $provider = null;
    private ErrorResponseGeneratorInterface $generator;
    
    public function __construct(ErrorResponseGeneratorInterface $generator, EventDispatcherInterface $dispatcher = null)
    {
        $this->errorLevel(E_ALL);
        $this->setGenerator($generator);
        $this->setDispatcher($dispatcher ?? new EventDispatcher());
    }
    
    /**
     * Set error_reporting level or return current level if no level parameter is given. 
     * @param int|null $level
     * @return int
     */
    public function errorLevel(?int $level = null): int 
    {
        if ($level != null)
        {
            $this->errorLevel = $level;
        }
        
        return $this->errorLevel;
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
        $old = error_reporting($this->errorLevel);
        set_error_handler($this->createHandler());

        try
        {
           $response = $handler->handle($request);
        }

        catch (Throwable $e)
        {
            $response = $this->handleException($e, $request);
        }
        
        restore_error_handler();
        error_reporting($old);

        return $response;
    }
    
    /**
     * Handle exception and fire ErrorEvent
     * @param Throwable $e
     * @return ResponseInterface
     */                                             
    public function handleException(Throwable $e, ?ServerRequestInterface $request = null): ResponseInterface
    {
        $request = $request ?? ServerRequestFactory::fromGlobals();
        $response = $this->generator->generate($e, $request);
            
        if ($this->dispatcher)
        {
            $response = $this->dispatcher
                ->dispatch(new ErrorEvent($e, $request, $response))
                ->response();
        }
        
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
