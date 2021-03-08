<?php

namespace Bermuda\ErrorHandler;

/**
 * @param \Throwable $e
 * @return int
 */
function get_status_code_from_throwable(\Throwable $e): int
{
    return $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500 ;
}
