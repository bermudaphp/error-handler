<?php

namespace Bermuda\ErrorHandler;

interface ErrorListenerInterface
{
    public function getPriority(): int ;
    public function handleEvent(ErrorEvent $event): void ;
}
