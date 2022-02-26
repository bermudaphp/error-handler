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

final class WhoopsRenderer implements ErrorRendererInterface, ServerRequestAwareInterface
{
    private RunInterface $whoops;
    private ?ServerRequestInterface $request = null;
  
    public function __construct(RunInterface $whoops = new Run)
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
    
    public function setServerRequest(ServerRequestInterface $request): ServerRequestAwareInterface
    {
        $this->request = $request;
        return $this;
    }
    
    private function addHandler(RunInterface $whoops): RunInterface
    {
        if (Misc::isCommandLine()) {
            $whoops->pushHandler(new PlainTextHandler);
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
