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

/**
 * Class ErrorHandler
 * @package Bermuda\ErrorHandler
 */
final class ErrorHandler implements ErrorHandlerInterface, ErrorRendererInterface
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
    
    /**
     * @inheritDoc
     */                                             
    public function handleException(Throwable $e): void
    {
        if ($e instanceof HttpException)
        {
            $response = $this->generator->generate($e);
            $response = $this->dispatcher->dispatch(
                new HttpErrorEvent($e, $e->getServerRequest(), $response)
            )
                ->response();
            
            $this->emitter->emit($response);
            exit;
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
        return $this->renderer->renderException($e);
    }
}
