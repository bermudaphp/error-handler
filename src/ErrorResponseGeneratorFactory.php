<?php

namespace Bermuda\ErrorHandler;

use Bermuda\ErrorHandler\Generator\ErrorResponseGenerator;
use Bermuda\ErrorHandler\Generator\WhoopsErrorGenerator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class ErrorResponseGeneratorFactory 
{
    public function __invoke(ContainerInterface $container): ErrorResponseGenerator
    {
        return new ErrorResponseGenerator($container->get(ResponseFactoryInterface::class), $container->get(WhoopsErrorGenerator::class));
    }
}
