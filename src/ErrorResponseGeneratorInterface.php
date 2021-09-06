<?php

namespace Bermuda\ErrorHandler;

use Psr\Http\Message\ResponseInterface;

interface ErrorResponseGeneratorInterface
{
    public function generate(ServerException $e): ResponseInterface;
}
