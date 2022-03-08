<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\HTTP\Emitter;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\EventDispatcherAwareInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;

final class ErrorHandler implements ErrorHandlerInterface, ErrorRendererInterface, EventDispatcherAwareInterface, ErrorResponseGeneratorInterface
{ 
    use ErrorHandlerTrait;
    private array $handlers = [];
    public function __construct(Generator\ErrorResponseGenerator $generator, private ServerRequestCreatorInterface $requestCreator, EmitterInterface $emitter = new Emitter,
        private ErrorRendererInterface $renderer = new WhoopsRenderer, EventDispatcherInterface $dispatcher = new EventDispatcher,
    ){
        $this->setDispatcher($dispatcher);
        $this->generator = $generator; $this->emitter = $emitter;
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
            $response = $this->generateResponse($e->throwable, $e->serverRequest);
            $this->emitter->emit($response);
            exit;
        }
        
        $content = $this->renderException($e);
        $this->dispatcher->dispatch(new ErrorEvent($e));
        
        exit($content);
    }

    /**
     * @param Throwable $e
     * @param ServerRequestInterface|null $request
     * @return ResponseInterface
     */
    public function generateResponse(Throwable $e, ServerRequestInterface $request = null): ResponseInterface
    {
        $this->dispatcher->dispatch($event = createEvent($e, $request ?? $this->requestCreator->fromGlobals()));
        return $this->generator->generateResponse($event->throwable, $event->serverRequest);
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
