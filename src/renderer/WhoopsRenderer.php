<?php

namespace Bermuda\ErrorHandler\Renderer;

use Throwable;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;
use Whoops\Handler\PlainTextHandler;
use Bermuda\ErrorHandler\ErrorRendererInterface;

/**
 * Class WhoopsRenderer
 * @package Bermuda\ErrorHandler\Renderer
 */
final class WhoopsRenderer implements ErrorRendererInterface
{
    private RunInterface $whoops;
  
    public function __construct(RunInterface $whoops)
    {
        $this->whoops = $whoops;
    }
  
    public function setWhoops(RunInterface $whoops): self
    {
        $this->whoops = $whoops;
        return $this;
    }

    public static function plainTextRendering(): self
    {
        return new self(self::getWhoops()->pushHandler(new PlainTextHandler));
    }

    public static function prettyPageRendering(): self
    {
        return new self(self::getWhoops()->pushHandler(new PrettyPageHandler()));
    }

    public static function jsonRendering(): self
    {
        return new self(self::getWhoops()->pushHandler(new JsonResponseHandler()));
    }

    private static function getWhoops(): Run
    {
        $whoops = new Run();

        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);

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
