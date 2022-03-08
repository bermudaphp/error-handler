<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\EventDispatcherAwareInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Bermuda\Eventor\Provider\PrioritizedListenerProviderInterface;

trait ErrorHandlerTrait
{
    private EventDispatcherInterface $dispatcher;
    private ?PrioritizedListenerProviderInterface $provider = null;
   
    public function __construct(private ErrorResponseGeneratorInterface $generator, private EmitterInterface $emitter,
        EventDispatcherInterface $dispatcher = new EventDispatcher
    ){
        $this->setDispatcher($dispatcher);
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
     * @return static
     */
    public function on(ErrorListenerInterface $listener): ErrorHandlerInterface
    {
        $this->provider->listen(ErrorEvent::class, $listener->handleEvent(...), $listener->getPriority());
        return $this;
    }
}
