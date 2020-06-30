<?php


namespace Bermuda\ErrorHandler;


/**
 * Interface ErrorListenerInterface
 * @package Bermuda\ErrorHandler
 */
interface ErrorListenerInterface
{
    /**
     * @param ErrorEvent $event
     */
    public function __invoke(ErrorEvent $event): void ;
}
