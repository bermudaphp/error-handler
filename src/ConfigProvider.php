<?php

namespace Bermuda\ErrorHandler;

/**
 * Class ConfigProvider
 * @package Bermuda\ErrorHandler
 */
final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            ErrorResponseGeneratorInterface::class => ErrorResponseGeneratorFactory::class,
            ErrorHandlerInterface::class => ErrorHandlerFactory::class
        ];
    }
    
    /**
     * @inheritDoc
     */
    protected function getAliases(): array
    {
        return [ErrorRendererInterface::class => ErrorHandlerInterface::class];
    }
}
