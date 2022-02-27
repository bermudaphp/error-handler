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
use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Psr\Http\Message\ServerRequestInterface;

final class WhoopsRenderer implements ErrorRendererInterface, ServerRequestAwareInterface
{
    private $configurator = null;
    private ?ServerRequestInterface $request = null;
    public function __construct(private RunInterface $whoops = new Run, callable $configurator = null)
    {
        $this->setWhoops($whoops)->configurator = $configurator;
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
    
    public function setServerRequest(ServerRequestInterface $serverRequest): ServerRequestAwareInterface
    {
        $this->request = $serverRequest;
        return $this;
    }
    
    public function setHandlerConfigurator(callable $configurator): self
    {
        $this->configurator = $configurator;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderException(Throwable $e): string
    {
        if ($this->request != null && $this->configurator != null) {
            foreach($this->whoops->getHandlers() as $handler) ($this->configurator)($handler, $this->request);
        }
        
        return $this->whoops->handleException($e);
    }
}
