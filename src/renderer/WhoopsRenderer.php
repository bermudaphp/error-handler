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
    public function __construct(private RunInterface $whoops = new Run)
    {
        $this->setWhoops($whoops);
    }
  
    public function setWhoops(RunInterface $whoops): self
    {
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        
        if (count($whoops->getHandlers()) == 0) {
            $this->addHandler($whoops);
        }

        $this->whoops = $whoops;
        
        return $this;
    }
    
    private function addHandler(RunInterface $whoops): RunInterface
    {
        if (Misc::isCommandLine()) {
            $whoops->pushHandler(new PlainTextHandler);
            return $whoops;
        }

        if (Misc::isAjaxRequest()) {
            $whoops->pushHandler(new JsonResponseHandler);
            return $whoops;
        }
        
        $whoops->pushHandler(new PrettyPageHandler);
        return $whoops;
    }

    /**
     * @inheritDoc
     */
    public function renderException(Throwable $e): string
    {
        return $this->whoops->handleException($e);
    }
}
