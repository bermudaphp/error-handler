<?php

namespace Bermuda\ErrorHandler;

use Bermuda\HTTP\Emitter;
use Bermuda\Eventor\EventDispatcher;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Container\ContainerInterface;
use Bermuda\Eventor\EventDispatche;
use Bermuda\Eventor\EventDispatcherInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Bermuda\ErrorHandler\Generator\ErrorResponseGenerator;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

final class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        return new ErrorHandler(
            $container->get(ErrorResponseGenerator::class),
            $container->get(ServerRequestCreatorInterface::class),
            $container->has(EmitterInterface::class) ? $container->get(EmitterInterface::class) : new Emitter,
            $container->has(ErrorRendererInterface::class) ? $container->get(ErrorRendererInterface::class) : new WhoopsRenderer,
            $container->has(EventDispatcherInterface::class) ? $container->get(EventDispatcherInterface::class) : new EventDispatcher()
        );
    }
}
