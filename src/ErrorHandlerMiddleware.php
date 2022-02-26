<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\Eventor\EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\Eventor\Provider\PrioritizedProvider;

final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    use ErrorHandlerTrait;
    
    public function __construct(private ErrorResponseGeneratorInterface $generator, 
        private EventDispatcherInterface $dispatcher = null, int $errorLevel = E_ALL
    )
    {
        $this->setResponseGenerator($generator)
            ->setDispatcher($dispatcher ?? new EventDispatcher())
            ->errorLevel($errorLevel);
    }
    
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $old = error_reporting($this->errorLevel);
        set_error_handler(static function(int $errno, string $msg, string $file, int $line): void {
            if ((error_reporting() & $errno)) {
                throw new \ErrorException($msg, 0, $errno, $file, $line);
            }
        });

        try {
            $response = $handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->generator->generateResponse($e, $request);
        }
        
        restore_error_handler();
        error_reporting($old);
        
        return $response;
    }
}
