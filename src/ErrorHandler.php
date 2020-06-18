<?php


namespace Lobster;


use Lobster\Contracts\ErrorResponseGenerator;
use Lobster\Contracts\ErrorListener;
use Lobster\Events\Dispatcher;
use Lobster\Events\EventDispatcher;
use Lobster\Events\Providers\Provider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


/**
 * Class ErrorHandler
 * @package Lobster
 */
class ErrorHandler implements MiddlewareInterface
{
    private EventDispatcher $dispatcher;
    private ErrorResponseGenerator $generator;

    public function __construct(ErrorResponseGenerator $generator, EventDispatcher $dispatcher = null)
    {
        $this->setDispatcher($dispatcher)->generator = $generator;
    }

    /**
     * @param EventDispatcher|null $dispatcher
     * @return $this
     */
    private function setDispatcher(?EventDispatcher $dispatcher) : self
    {
        if (!$dispatcher)
        {
            $dispatcher = new Dispatcher();
        }

        $this->dispatcher = $dispatcher->attach(new Provider(Contracts\ErrorEvent::class));
        
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
        set_error_handler(function(int $errno, string $msg, string $file, int $line)
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
