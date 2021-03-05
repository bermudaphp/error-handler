<?php

namespace Bermuda\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherFactory;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\EventDispatcherFactoryInterface;

/**
 * Class ErrorHandlerMiddleware
 * @package Bermuda\ErrorHandler
 */
final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private EventDispatcherInterface $dispatcher;
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
        $this->dispatcher = $dispatcher->attach($this->provider);
        return $this;
    }
    
    /**
     * @param ErrorResponseGenerator $generator
     * @return self
     */
    public function setGenerator(ErrorResponseGenerator $generator): self
    {
        $this->generator = $generator;
        return $this;
    }
    
    /**
     * @param ErrorListenerInterface $listener
     * @return void
     */
    public function listen(ErrorListenerInterface $listener): void
    {
        $this->provider->listen($listener);
    }
    
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler(static function(int $errno, string $msg, string $file, int $line)
        {
            if (!(error_reporting() & $errno))
            {
                return;
            }

            throw new \ErrorException($msg, 0, $errno, $file, $line);
        });

        try
        {
           $response = $handler->handle($request);
        }

        catch (\Throwable $e)
        {
            $response = $this->generator->generate($e, $request);
            
            if ($this->dispatcher)
            {
                $response = $this->dispatcher->dispatch(
                    new ErrorEvent($e, $request, $response))
                    ->getResponse();
            }
        }
        
        restore_error_handler();

        return $response;
    }
}
