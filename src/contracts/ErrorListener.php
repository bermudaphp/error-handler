<?php


namespace Lobster\Contracts;


/**
 * Interface ErrorListener
 * @package Lobster\Contracts
 */
interface ErrorListener
{
    /**
     * @param ErrorEvent $event
     */
    public function __invoke(ErrorEvent $event) : void ;
}