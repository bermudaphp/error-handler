<?php


namespace Bermuda\ErrorHandler;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherFactory;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\EventDispatcherFactoryInterface;


/**
 * Class ErrorHandlerMiddleware
 * @package Bermuda\ErrorHandler
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private EventDispatcherInterface $dispatcher;
    private ErrorResponseGeneratorInterface $generator;

    public function __construct(ErrorResponseGeneratorInterface $generator, EventDispatcherFactoryInterface $factory = null)
    {
        $this->setEventDispatcherFromFactory($factory)
            ->generator = $generator;
    }
    
    /**
     * @param int $level
     * @return int
     */
    public function setErrorLevel(int $level): int 
    {
        return error_reporting($level);
    }

    /**
     * @param EventDispatcherFactoryInterface|null $factory
     * @return $this
     */
    private function setEventDispatcherFromFactory(?EventDispatcherFactoryInterface $factory): self
    {
        if (!$factory)
        {
            $factory = new EventDispatcherFactory;
        }

        $this->dispatcher = $factory->make();
        
        return $this;
    }

    /**
     * @param ErrorListenerInterface $listener
     * @return $this
     */
    public function listen(ErrorListenerInterface $listener): self
    {
        foreach ($listeners as $listener)
        {
            $this->dispatcher->getProvider()->listen(ErrorEvent::class, $listener);
        }

        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler(static function(int $errno, string $msg, string $file, int $line)
        {
            if (! (error_reporting() & $errno))
            {
                return;
            }

            throw new \ErrorException($msg, 0, $errno, $file, $line);
        });

        try
        {
           $response = $handler->handle($request);
        }

        catch (\Throwable $e)
        {
            $response = $this->generator->generate($e, $request);
            $this->dispatcher->dispatch(new ErrorEvent($e, $request, $response));
        }
        
        restore_error_handler();

        return $response;
    }
}
