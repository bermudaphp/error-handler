<?php

namespace Bermuda\ErrorHandler;

interface ErrorListenerInterface
{
    /**
     * @param ErrorEvent $event
     */
    public function handleEvent(ErrorEvent $event): void ;
}
