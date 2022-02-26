<?php

namespace Bermuda\ErrorHandler;

use Psr\Container\ContainerInterface;
use Bermuda\Eventor\EventDispatche;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

final class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        return new ErrorHandler(
            $container->get(ErrorResponseGeneratorInterface::class), $container->get(EmitterInterface::class),
            $container->has(ErrorRendererInterface::class) ? $container->get(ErrorRendererInterface::class) : new WhoopsRenderer,
            $container->has(EventDispatcherInterface::class) ? $container->get(EventDispatcherInterface::class) : new EventDispatcher
        );
    }
}
