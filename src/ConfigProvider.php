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
            EmitterInterface::class => EmitterFactory::class,
            ErrorResponseGeneratorInterface::class => ErrorResponseGeneratorFactory::class
        ];
    }
}
