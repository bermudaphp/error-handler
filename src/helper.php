<?php

namespace Bermuda\ErrorHandler;

/**
 * @param \Throwable $e
 * @return int
 */
function get_status_code_from_throwable(\Throwable $e): int
{
    return RequestHandlingException::getStatusCode($e);
}
