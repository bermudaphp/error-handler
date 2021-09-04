<?php

namespace Bermuda\ErrorHandler;

interface ErrorListenerInterface
{
    /**
     * @param ErrorEvent $event
     */
    public function __invoke(ErrorEvent $event): void ;
}
