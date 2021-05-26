<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;
use Bermuda\RequestHandlerRunner\ServerRequestFactory;

/**
 * Class ErrorHandlerMiddleware
 * @package Bermuda\ErrorHandler
 */
final class ErrorHandlerMiddleware implements MiddlewareInterface, ErrorHandlerInterface, ErrorRendererInterface
{
    private int $errorLevel;
    private ErrorRendererInterface $renderer;
    private ResponseFactoryInterface $factory;
    private EventDispatcherInterface $dispatcher;
    private ?PrioritizedProvider $provider = null;
   
    public function __construct(ResponseFactoryInterface $factory, 
        ErrorRendererInterface $renderer = null, EventDispatcherInterface $dispatcher = null, 
        int $errorLevel = E_ALL
    )
    {
        $this->setResponseFactory($factory)
            ->setRenderer($renderer ?? new Renderer\WhoopsRenderer())
            ->setDispatcher($dispatcher ?? new EventDispatcher())
            ->errorLevel($errorLevel);
    }
    
    public function setResponseFactory(ResponseFactoryInterface $factory): self 
    {
        $this->factory = $factory;
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
        if (!$this->provider)
        {
            $this->provider = new PrioritizedProvider();
        }
        
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
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $old = error_reporting($this->errorLevel);
        set_error_handler($this->createHandler());

        try {
           $response = $handler->handle($request);
        }

        catch (Throwable $e)
        {
            $e = ServerRequestException::decorate($e, $request);
            $content = $this->renderException($e);
            
            ($response = $this->factory->createResponse($e->getCode()))
                ->getBody()->write($content);
        }
        
        restore_error_handler();
        error_reporting($old);

        return $response;
    }
    
    /**
     * @inheritDoc
     */                                             
    public function handleException(Throwable $e): void
    {
        $response = $this->generator->generate($e, $request = $request ?? ServerRequestFactory::fromGlobals());
        return $response = $this->dispatcher->dispatch(new ErrorEvent($e, $request, $response))->response();
    }
    
    /**
     * @inheritDoc
     */                                         
    public function renderException(Throwable $e): string
    {
        return $this->render->render($e);
    }
    
    private function createHandler(): callable
    {
        return new class
        {
            public function __invoke(int $errno, string $msg, string $file, int $line)
            {
                if (!(error_reporting() & $errno))
                {
                    return;
                }

                throw new \ErrorException($msg, 0, $errno, $file, $line);
            }
        };
    }
}
