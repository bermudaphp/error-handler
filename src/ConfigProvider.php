<?php

namespace Bermuda\ErrorHandler;

final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            ErrorResponseGeneratorInterface::class => ErrorResponseGeneratorFactory::class,
            ErrorHandlerInterface::class => ErrorHandlerFactory::class,
        ];
    }
    
    protected function getInvokables(): array
    {
        return [ErrorRendererInterface::class => Renderer\WhoopsErrorRenderer::class];
    }
}
