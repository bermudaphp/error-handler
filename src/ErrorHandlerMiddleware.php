<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Bermuda\Config\conf;

final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ErrorHandler $errorHandler,
        private int $errorLevel = E_ALL
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $old = error_reporting($this->errorLevel);
        set_error_handler(createExceptionHandlerCallback());

        try {
            $response = $handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->errorHandler->setServerRequest($request)
                ->generateResponse($e, true);
        }
        
        restore_error_handler();
        error_reporting($old);
        
        return $response;
    }

    /**
     * @param ErrorListenerInterface $listener
     * @return static
     */
    public function listen(Listener\ErrorListenerInterface $listener): self
    {
        $this->errorHandler->listen($listener);
        return $this;
    }

    public static function createFromContainer(ContainerInterface $container): self
    {
        return new ErrorHandlerMiddleware(
            $container->get(ErrorHandler::class),
            conf($container)->get(ConfigProvider::CONFIG_KEY_ERROR_LEVEL, E_ALL)
        );
    }
}
