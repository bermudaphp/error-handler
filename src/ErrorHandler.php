<?php

namespace Bermuda\ErrorHandler;

use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Bermuda\HTTP\Contracts\ServerRequestAwareTrait;
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

/**
 * @method self setServerRequest(ServerRequestInterface $serverRequest)
 */
final class ErrorHandler implements ErrorHandlerInterface, ErrorRendererInterface, EventDispatcherAwareInterface, ErrorResponseGeneratorInterface, ServerRequestAwareInterface
{
    private array $handlers = [];
    use ErrorHandlerTrait, ServerRequestAwareTrait;
    public function __construct(Generator\ErrorResponseGenerator $generator, EmitterInterface $emitter = new Emitter,
        private ErrorRendererInterface $renderer = new WhoopsRenderer, EventDispatcherInterface $dispatcher = new EventDispatcher,
    ){
        $this->setDispatcher($dispatcher);
        $this->generator = $generator; $this->emitter = $emitter;
    }
    
    public function registerHandler(ErrorHandlerInterface $handler): self
    {
        $this->handlers[] = $handler;
        return $this;
    }
    
    public function canGenerate(Throwable $e): bool
    {
        return true;
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
            $response = $this->generateResponse($e, true);
            $this->emitter->emit($response);
            exit;
        }
        
        $content = $this->renderException($e);
        $this->dispatcher->dispatch(new ErrorEvent($e));
        
        exit($content);
    }

    /**
     * @param Throwable $e
     * @param bool $dispatchEvent
     * @return ResponseInterface
     * @throws Throwable
     */
    public function generateResponse(Throwable $e, bool $dispatchEvent = false): ResponseInterface
    {
        $request = $e?->serverRequest ?? $this->serverRequest;
        if ($request != null) {
            $this->generator->setServerRequest($request);
        }

        if ($dispatchEvent) {
            $event = createEvent($e);
            $this->dispatcher->dispatch($event);
        }

        return $this->generator->generateResponse($event->throwable ?? ($e->throwable ?? $e));
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
