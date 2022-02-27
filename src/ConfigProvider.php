<?php

namespace Bermuda\ErrorHandler;

use Psr\Container\ContainerInterface;
use Generator\ErrorResponseGenerator;
use Generator\WhoopsErrorGenerator;
use Psr\Http\Message\ResponseFactoryInterface;

final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            ErrorHandler::class => ErrorHandlerFactory::class,
            ErrorResponseGenerator::class => fn(ContainerInterface $container) => new ErrorResponseGenerator($container->get(WhoopsErrorGenerator::class)),
            WhoopsErrorGenerator::class => fn(ContainerInterface $container) => new WhoopsErrorGenerator($container->get(ResponseFactoryInterface::class))
        ];
    }
    
    protected function getInvokables(): array
    {
        return [ErrorRendererInterface::class => Renderer\WhoopsRenderer::class];
    }
}
