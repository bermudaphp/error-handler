<?php

namespace Bermuda\ErrorHandler\Renderer;

use Throwable;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;
use Whoops\Handler\PlainTextHandler;
use Whoops\Util\Misc;
use Bermuda\ErrorHandler\ErrorRendererInterface;

final class WhoopsRenderer implements ErrorRendererInterface
{
    private RunInterface $whoops;
  
    public function __construct(RunInterface $whoops = null)
    {
        $whoops != null ?: $whoops = $this->addHandler(new Run);
        $this->setWhoops($whoops);
    }
  
    public function setWhoops(RunInterface $whoops): self
    {
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);

        $this->whoops = $whoops;
        
        return $this;
    }
    
    private function addHandler(RunInterface $whoops): RunInterface
    {
        if (Misc::isCommandLine()) {
            $whoops->pushHandler(new PrettyPageHandler);
            return $whops;
        }

        if (Misc::isAjaxRequest()) {
            $whoops->pushHandler(new JsonResponseHandler);
            return $whops;
        }
        
        $whoops->pushHandler(new PrettyPageHandler);
        return $whops;
    }

    /**
     * @inheritDoc
     */
    public function renderException(Throwable $e): string
    {
        return $this->whoops->handleException($e);
    }
}
