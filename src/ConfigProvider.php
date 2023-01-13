<?php

namespace Bermuda\ErrorHandler;

use Whoops\Run;
use Whoops\RunInterface;
use Bermuda\Config\Config;
use Psr\Container\ContainerInterface;
use Bermuda\ErrorHandler\Generator\ErrorResponseGenerator;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Bermuda\ErrorHandler\Generator\WhoopsErrorGenerator;

final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    const error_handling = 'error_handling';
    const error_level = 'error_level';
    const error_whoops_renderer_configurator = 'error_whoops_renderer_configurator';

    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            ErrorHandler::class => ErrorHandlerFactory::class,
            ErrorHandlerMiddleware::class => static function(ContainerInterface $container) {
                return new ErrorHandlerMiddleware($container->get(ErrorHandler::class), 
                    $container->get(Config::app_config)[self::error_handling][self::error_level] ?? E_ALL
                );
            },
            ErrorResponseGenerator::class => ErrorResponseGeneratorFactory::class,
            WhoopsErrorGenerator::class => static fn(ContainerInterface $container) => new WhoopsErrorGenerator($container->get(ResponseFactoryInterface::class)),
            WhoopsRenderer::class => static fn(ContainerInterface $container) => new WhoopsRenderer(
                $container->has(RunInterface::class) ? $container->get(RunInterface::class) : new Run,
                $container->get(Config::app_config)[self::error_handling][self::error_whoops_renderer_configurator] ?? null
            ),
        ];
    }
    
    protected function getAliases(): array
    {
        return [
            ErrorRendererInterface::class => WhoopsRenderer::class,
            ErrorResponseGeneratorInterface::class => ErrorResponseGenerator::class
        ];
    }
}
