<?php

namespace Bermuda\ErrorHandler;

use Bermuda\Eventor\EventDispatcherInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Container\ContainerInterface;

final class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        return new ErrorHandler(
            $container->get(ErrorResponseGeneratorInterface::class), $container->get(EmitterInterface::class),
            null, null,
            $container->get('config')['error']['errorLevel'] ?? E_ALL
        );
    }
}
