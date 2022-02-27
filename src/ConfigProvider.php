<?php

namespace Bermuda\ErrorHandler;

use Psr\Container\ContainerInterface;
use Generator\ErrorResponseGenerator;
use Generator\WhoopsErrorGenerator;
use Renderer\WhoopsRenderer;
use Whoops\RunInterface;
use Whoops\Run;
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
            WhoopsErrorGenerator::class => fn(ContainerInterface $container) => new WhoopsErrorGenerator($container->get(ResponseFactoryInterface::class)),
            WhoopsRenderer::class => fn(ContainerInterface $container) => new WhoopsRenderer(
                $container->has(RunInterface::class) ? $container->get(RunInterface::class) : new Run,
                $container->get('config')['error']['error.renderer.configurator'] ?? null
            ),
        ];
    }
    
    protected function getAliases(): array
    {
        return [ErrorRendererInterface::class => WhoopsRenderer::class];
    }
}
