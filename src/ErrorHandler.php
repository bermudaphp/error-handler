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
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

/**
 * Class ErrorHandlerMiddleware
 * @package Bermuda\ErrorHandler
 */
final class ErrorHandler implements ErrorHandlerInterface, ErrorRendererInterface
{
    private int $errorLevel;
    private EmitterInterface $emitter;
    private ErrorRendererInterface $renderer;
    private EventDispatcherInterface $dispatcher;
    private ?PrioritizedProvider $provider = null;
    private ErrorResponseGeneratorInterface $generator;
   
    public function __construct(ErrorResponseGeneratorInterface $generator, EmitterInterface $emitter, 
        ErrorRendererInterface $renderer = null, EventDispatcherInterface $dispatcher = null, 
        int $errorLevel = E_ALL
    )
    {
        $this->setResponseGenerator($generator)->setEmitter($emitter)
            ->setRenderer($renderer ?? new Renderer\WhoopsRenderer())
            ->setDispatcher($dispatcher ?? new EventDispatcher())
            ->errorLevel($errorLevel);
    }
    
    public function setResponseGenerator(ErrorResponseGeneratorInterface $generator): self 
    {
        $this->generator = $generator;
        return $this;
    }
    
    public function setEmitter(EmitterInterface $emitter): self 
    {
        $this->emitter = $emitter;
        return $this;
    }
    
    /**
     * Set error_reporting level or return current level if no level parameter is given. 
     * @param int|null $level
     * @return int
     */
    public function errorLevel(?int $level = null): int 
    {
        return $level != null ? $this->errorLevel = $level : $this->errorLevel;
    }
    
    /**
     * @param EventDispatcherInterface $dispatcher
     * @return self
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->provider ?: $this->provider = new PrioritizedProvider();
        $this->dispatcher = $dispatcher->attach($this->provider);
        
        return $this;
    }
    
    /**
     * @param ErrorResponseGenerator $generator
     * @return self
     */
    public function setRenderer(ErrorRendererInterface $renderer): self
    {
        $this->renderer = $renderer;
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
     * @inheritDoc
     */                                             
    public function handleException(Throwable $e): void
    {
        if ($e instanceof RequestHandlingException)
        {
            $response = $this->generator->generate($e);
            $response = $this->dispatcher(new HttpErrorEvent($e, $request, $response))->response();
            
            $this->emitter->emit($response);
            return;
        }
        
        $content = $this->renderException($e);
        $this->dispatcher->dispatch(new ErrorEvent($e));
        
        die($content);
    }
    
    /**
     * @inheritDoc
     */                                         
    public function renderException(Throwable $e): string
    {
        return $this->render->render($e);
    }
  
    private function generateResponse(RequestHandlingException $e): ResponseInterface
    {
        return $this->dispatcher(new HttpErrorEvent($e, $request, $this->generator->generate($e)))->response();
    }
}
