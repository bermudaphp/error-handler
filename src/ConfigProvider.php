<?php

namespace Bermuda\ErrorHandler;

final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [ErrorResponseGeneratorInterface::class => ErrorResponseGeneratorFactory::class];
    }
}
