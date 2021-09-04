<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;

trait ErrorHandlerTrait
{
    private int $errorLevel;
    private EventDispatcherInterface $dispatcher;
    private ?PrioritizedProvider $provider = null;
    private ErrorResponseGeneratorInterface $generator;
   
    public function __construct(ErrorResponseGeneratorInterface $generator,
        EventDispatcherInterface $dispatcher = null, int $errorLevel = E_ALL
    )
    {
        $this->setResponseGenerator($generator)
            ->setDispatcher($dispatcher ?? new EventDispatcher())
            ->errorLevel($errorLevel);
    }
    
    public function setResponseGenerator(ErrorResponseGeneratorInterface $generator): self 
    {
        $this->generator = $generator;
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
     * @param ErrorListenerInterface|HttpErrorListenerInterface $listener
     * @return void
     */
    public function listen(ErrorListenerInterface $listener, int $priority = 0): void
    {
        $this->provider->listen(ErrorEvent::class, $listener, $priority);
    }
    
    private function generateResponse(ServerException $e): ResponseInterface
    {
        return $this->dispatcher(new ServerErrorEvent($e, $request, $this->generator->generate($e)))->response();
    }
}
