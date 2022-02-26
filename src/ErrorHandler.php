<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

final class ErrorHandler implements ErrorHandlerInterface
{
    use ErrorHandlerTrait;
    
    private EmitterInterface $emitter;
   
    public function __construct(ErrorResponseGeneratorInterface $generator, EmitterInterface $emitter, 
        ErrorRendererInterface $renderer = null, EventDispatcherInterface $dispatcher = null, 
        int $errorLevel = E_ALL
    )
    {
        $this->setResponseGenerator($generator)->setEmitter($emitter)
            ->setRenderer($renderer ?? Renderer\WhoopsRenderer::chooseForSapi())
            ->setDispatcher($dispatcher ?? new EventDispatcher())
            ->errorLevel($errorLevel);
    }
    
    public function setEmitter(EmitterInterface $emitter): self 
    {
        $this->emitter = $emitter;
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
    
    public function registerHandler(ErrorHandlerInterface $handler): self
    {
        $this->handlers[] = $handler;
    }
    
    /**
     * @inheritDoc
     */                                             
    public function handleException(Throwable $e): never
    {
        foreach($this->handlers as $handler) {
            if ($handler->canHandle($e)) {
                if ($handler instanceof EventDispatcherAwareInterface) {
                    $handler->setDispatcher($this->dispatcher)->handleException($e);
                }
                
                $this->dispatcher->dispatch(createEvent($e));
                $handler->handleException($e);
            }
        }
        
        if ($e instanceof ServerException) {
            $this->dispatcher->dispatch(new ServerErrorEvent($e->getPrevious(), $e->getServerRequest()));
            $this->emitter->emit($this->generator->generate($e));
            exit;
        }
        
        $content = $this->renderException($e);
        $this->dispatcher->dispatch(new ErrorEvent($e));
        
        exit($content);
    }
    
    /**
     * @inheritDoc
     */                                         
    public function renderException(Throwable $e): string
    {
        return $this->renderer->renderException($e);
    }
    
    public function canHandle(Throwable $e): bool
    {
        return true;
    }
}
