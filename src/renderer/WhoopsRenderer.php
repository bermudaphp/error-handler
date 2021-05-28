<?php

namespace Bermuda\ErrorHandler\Renderer;

use Throwable;
use Whoops\Run;
use Whoops\RunInterface;
use Whoops\Handler\PlainTextHandler;

/**
 * Class WhoopsRenderer
 * @package Bermuda\ErrorHandler\Renderer
 */
final class WhoopsRenderer implements ErrorHandlerInterface
{
    private ?RunInterface $whoops = null;
  
    public function __construct(RunInterface $whoops = null)
    {
        $this->whoops = $whoops ?? $this->getWhoops();
    }
  
    public function setWhoops(RunInterface $whoops): self
    {
        return $this;
    }
  
    public function getWhoops(): RunInterface
    {
        if ($this->whoops == null)
        {
            $whoops = new Run();
            $whoops->pushHandler(new PlainTextHandler);
            $whoops->allowQuit(false);
            $whoops->writeToOutput(false);
          
            return $whoops;
        }
      
        return $this->whoops;
    }
  
    /**
     * @inheritDoc
     */
    public function renderException(Throwable $e): string
    {
        return $this->whoops->handleException($e);
    }
}
