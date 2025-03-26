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
    public const string CONFIG_KEY_ERROR_LEVEL = 'Bermuda\ErrorHandler::error_level';
    public const string CONFIG_KEY_GENERATORS = 'Bermuda\ErrorHandler::generators';
    public const string CONFIG_KEY_CONFIGURATOR = 'Bermuda\ErrorHandler::configurator';

    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            ErrorHandler::class => [ErrorHandler::class, 'createFromContainer'],
            ErrorHandlerMiddleware::class => [ErrorHandlerMiddleware::class, 'createFromContainer'],
            ErrorResponseGenerator::class => [ErrorResponseGenerator::class, 'createFromContainer'],
            WhoopsErrorGenerator::class => [WhoopsErrorGenerator::class, 'createFromContainer'],
            WhoopsRenderer::class => [WhoopsRenderer::class, 'createFromContainer'],
        ];
    }
    
    protected function getAliases(): array
    {
        return [
            Renderer\ErrorRendererInterface::class => WhoopsRenderer::class,
            Generator\ErrorResponseGeneratorInterface::class => ErrorResponseGenerator::class
        ];
    }
}
