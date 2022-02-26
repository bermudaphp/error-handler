<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Renderer\WhoopsRenderer;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\EventDispatcherAwareInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

final class ErrorHandler implements ErrorHandlerInterface, ErrorRendererInterface
{ 
    private PrioritizedProvider $provider;
    public function __construct(private ErrorResponseGeneratorInterface $generator, private EmitterInterface $emitter, 
        private ErrorRendererInterface $renderer = new WhoopsRenderer, private EventDispatcherInterface $dispatcher = new EventDispatcher
    ){
        $this->dispatcher = $this->dispatcher->attach($this->provider = new PrioritizedProvider);
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
        
        $event = createEvent($e);
        
        if ($event instanceof ServerErrorEvent) {
            $this->dispatcher->dispatch($event);
            $response = $this->generator->generateResponse(
                $event->getThrowable(), $event->getServerRequest()
            );
            $this->emitter->emit($response);
            exit;
        }
        
        $content = $this->renderException($e);
        $this->dispatcher->dispatch($event);
        
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
