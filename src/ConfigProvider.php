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
            RequestFactoryInterface::class => $psr17factory = static function()
            {
                return new Psr17Factory();
            },
            UriFactoryInterface::class => $psr17factory,
            ServerRequestFactoryInterface::class => $psr17factory,
            ResponseFactoryInterface::class => $psr17factory,
            UploadedFileFactoryInterface::class => $psr17factory,
            StreamFactoryInterface::class => $psr17factory,
            EmitterInterface::class => EmitterFactory::class,
            RequestHandlerRunner::class => RequestHandlerRunnerFactory::class,
            ErrorResponseGeneratorInterface::class => ErrorResponseGeneratorFactory::class
        ];
    }
}
