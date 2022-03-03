<?php

namespace Bermuda\ErrorHandler;

use Bermuda\ErrorHandler\Generator\ErrorResponseGenerator;
use Bermuda\ErrorHandler\Generator\WhoopsErrorGenerator;
use Psr\Container\ContainerInterface;

final class ErrorResponseGeneratorFactory 
{
    public function __invoke(ContainerInterface $container): ErrorResponseGenerator
    {
        return new ErrorResponseGenerator($container->get(WhoopsErrorGenerator::class));
    }
}
