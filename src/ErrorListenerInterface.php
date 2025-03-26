<?php

namespace Bermuda\ErrorHandler;

interface ErrorListenerInterface
{
    public int $priority {
        get;
    }

    public function handleEvent(ErrorEvent $event): void ;
}
