<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\HTTP\Emitter;
use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Bermuda\HTTP\Contracts\ServerRequestAwareTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherFactoryInterface;
use Bermuda\Eventor\EventDispatcherFactory;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\EventDispatcherAwareInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Bermuda\ErrorHandler\Renderer\ErrorRendererInterface;
use Bermuda\ErrorHandler\Generator\ErrorResponseGenerator;
use Bermuda\ErrorHandler\Generator\ErrorResponseGeneratorInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

/**
 * @method self setServerRequest(ServerRequestInterface $serverRequest)
 */
final class ErrorHandler implements ErrorHandlerInterface, ErrorRendererInterface,
    EventDispatcherAwareInterface, ErrorResponseGeneratorInterface,
    ServerRequestAwareInterface
{
    use ServerRequestAwareTrait;

    private array $handlers = [];
    private PrioritizedProvider $provider;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        private Generator\ErrorResponseGenerator $generator,
        private EmitterInterface $emitter = new Emitter,
        private Renderer\ErrorRendererInterface $renderer = new WhoopsRenderer,
        EventDispatcherFactoryInterface $dispatcherFactory = new EventDispatcherFactory
    ) {
        $this->provider = new PrioritizedProvider;
        $this->dispatcher = $dispatcherFactory->makeDispatcher([$this->provider]);
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
                    $handler->setDispatcher($this->dispatcher);
                }
                
                if ($this->serverRequest != null && $handler instanceof ServerRequestAwareInterface) {
                    $handler->setServerRequest($this->serverRequest);
                }
                
                $this->dispatcher->dispatch(new ErrorEvent($e, $this->serverRequest));
                $handler->handleException($e);
            }
        }
        
        if ($this->serverRequest != null) {
            $response = $this->generateResponse($e, true);
            $this->emitter->emit($response);
            exit;
        }
        
        $content = $this->renderException($e);
        $this->dispatcher->dispatch(new ErrorEvent($e));

        if (PHP_SAPI != 'cli') {
            http_response_code(getErrorCode($e));
        }
        
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
        if ($this->serverRequest) $this->generator->setServerRequest($this->serverRequest);

        $response = $this->generator->generateResponse($e);

        if ($dispatchEvent) {
            $event = $this->dispatcher->dispatch(new ErrorEvent($e, $this->serverRequest), $response);
            return $event->response ?? $response;
        }

        return $response;
    }

    /**
     * @inheritDoc
     */                                         
    public function renderException(Throwable $e): string
    {
        if ($this->serverRequest != null && $this->renderer instanceof ServerRequestAwareInterface) {
            $this->renderer->setServerRequest($this->serverRequest);
        }
        
        return $this->renderer->renderException($e);
    }
    
    public function canHandle(Throwable $e): bool
    {
        return true;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return static
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
     * @return $this
     */
    public function listen(Listener\ErrorListenerInterface $listener): ErrorHandlerInterface
    {
        $this->provider->listen(ErrorEvent::class, $listener->handleEvent(...), $listener->priority);
        return $this;
    }

    public static function createFromContainer(ContainerInterface $container): ErrorHandler
    {
        return new ErrorHandler(
            $container->get(ErrorResponseGenerator::class),
            $container->has(EmitterInterface::class) ? $container->get(EmitterInterface::class) : new Emitter,
            $container->has(ErrorRendererInterface::class) ? $container->get(ErrorRendererInterface::class) : new WhoopsRenderer,
            $container->has(EventDispatcherFactoryInterface::class) ? $container->get(EventDispatcherFactoryInterface::class) : new EventDispatcherFactory
        );
    }
}
