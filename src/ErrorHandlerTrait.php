<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;

trait ErrorHandlerTrait
{
    private EventDispatcherInterface $dispatcher;
    private ?PrioritizedProvider $provider = null;
   
    public function __construct(private ErrorResponseGeneratorInterface $generator, 
        EventDispatcherInterface $dispatcher = null
    ){
        $this->setDispatcher($dispatcher ?? new EventDispatcher);
    }
    
    /**
     * @param EventDispatcherInterface $dispatcher
     * @return self
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        if ($this->provider == null) {
            $this->provider = new PrioritizedProvider;
        }

        $this->dispatcher = $dispatcher->attach($this->provider);
        return $this;
    }

    /**
     * @param ErrorListenerInterface $listener
     * @return void
     */
    public function on(ErrorListenerInterface $listener, int $priority = 0): ErrorHandlerInterface
    {
        $this->provider->listen(ErrorEvent::class, $listener, $priority);
        return $this;
    }
}
