<?php

namespace Bermuda\ErrorHandler;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Container\ContainerInterface;

/**
 * Class ErrorHandlerFactory
 * @package Bermuda\ErrorHandler
 */
final class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        return new ErrorHandler(
            $container->get(ErrorResponseGeneratorInterface::class),
            $container->get(EmitterInterface::class),
        );
    }
}