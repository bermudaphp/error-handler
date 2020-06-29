<?php


namespace Bermuda\ErrorHandler;


use Lobster\Events\Dispatcher;
use Lobster\Events\EventDispatcher;
use Lobster\Events\Providers\Provider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\ErrorHandler\ErrorListenerInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorIntreface;


/**
 * Class ErrorHandlerMiddleware
 * @package Bermuda\ErrorHandler
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private EventDispatcherInterface $dispatcher;
    private ErrorResponseGeneratorInterface $generator;

    public function __construct(ErrorResponseGeneratorInterface $generator, EventDispatcherInterface $dispatcher = null)
    {
        $this->setDispatcher($dispatcher)->generator = $generator;
    }

    /**
     * @param EventDispatcher|null $dispatcher
     * @return $this
     */
    private function setDispatcher(?EventDispatcherInterface $dispatcher) : self
    {
        if (!$dispatcher)
        {
            $dispatcher = new Dispatcher();
        }

        $this->dispatcher = $dispatcher->attach(new Provider(ErrorEvent::class));
        
        return $this;
    }

    /**
     * @param ErrorListener ... $listeners
     * @return $this
     */
    public function listen(ErrorListener ... $listeners) : self
    {
        foreach ($listeners as $listener)
        {
            $this->dispatcher->getProvider(Contracts\ErrorEvent::class)->listen(Contracts\ErrorEvent::class, $listener);
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

        return $response;
    }
}
