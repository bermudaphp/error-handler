<?php

namespace Bermuda\ErrorHandler;

use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Psr\Log\LoggerInterface;

/**
 * Class LogErrorListener
 * @package Bermuda\ErrorHandler
 */
class LogErrorListener implements ErrorListenerInterface
{
    private LoggerInterface $logger;
    private ErrorRendererInterface $renderer;

    public function __construct(LoggerInterface $logger, ErrorRendererInterface $renderer = null)
    {
        $this->logger = $logger;
        $this->renderer = $renderer ?? WhoopsRenderer::plainTextRendering();
    }

    /**
     * @param ErrorEvent $event
     */
    public function __invoke(ErrorEvent $event): void
    {
        $this->logger->error($this->renderer->renderException($event->getThrowable()));
    }
}
