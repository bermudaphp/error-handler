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
        $generator = new ErrorResponseGenerator($container->get(ResponseFactoryInterface::class), $container->get(WhoopsErrorGenerator::class));
        $generators = $container->get('config')['errors']['generators'] ?? null;
        if (is_iterable($generators)) {
            foreach ($generators as $g) $generator->addGenerator($g);
        }

        return $generator;
    }
}
