<?php

namespace Bermuda\ErrorHandler;
use Throwable;

/**
 * @param Throwable $e
 * @return int
 */
function get_status_code_from_throwable(Throwable $e): int
{
    return ServerException::getStatusCode($e);
}

function get_error_code(int $code): int
{
    return $code >= 400 && $code < 600 ? $code : 500 ;
}
