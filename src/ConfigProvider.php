<?php

namespace Bermuda\ErrorHandler;

use Whoops\Run;
use Whoops\RunInterface;
use Generator\WhoopsErrorGenerator;
use Psr\Container\ContainerInterface;
use Generator\ErrorResponseGenerator;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;

final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            ErrorHandler::class => ErrorHandlerFactory::class,
            ErrorResponseGenerator::class => static fn(ContainerInterface $container) => new ErrorResponseGenerator($container->get(WhoopsErrorGenerator::class)),
            WhoopsErrorGenerator::class => static fn(ContainerInterface $container) => new WhoopsErrorGenerator($container->get(ResponseFactoryInterface::class)),
            WhoopsRenderer::class => static fn(ContainerInterface $container) => new WhoopsRenderer(
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
