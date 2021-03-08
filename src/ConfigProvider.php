<?php

namespace Bermuda\ErrorHandler;

use Whoops\RunInterface;
use Psr\Container\ContainerInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Bermuda\Templater\RendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Generator\WhoopsErrorGenerator;
use Bermuda\ErrorHandler\Generator\TemplateErrorGenerator;
use Bermuda\RequestHandlerRunner\RequestHandlerRunnerFactory;

final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            EmitterInterface::class => EmitterFactory::class,
            ErrorResponseGeneratorInterface::class => ErrorResponseGeneratorFactory::class
        ];
    }
}
