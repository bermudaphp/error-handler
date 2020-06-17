<?php


namespace Lobster;


/**
 * @param \Throwable $e
 * @return int
 */
function status_code(\Throwable $e) : int
{
    return $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500 ;
}