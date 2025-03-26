<?php

namespace Bermuda\ErrorHandler\Listener;

interface ErrorListenerInterface
{
    public int $priority {
        get;
    }

    public function handleEvent(ErrorEvent $event): void ;
}
