<?php

namespace Bermuda\ErrorHandler\Renderer;

use Throwable;

interface ErrorRendererInterface
{
    /**
     * @param Throwable $e
     * @return string
     */
    public function renderException(Throwable $e): string ;
}
