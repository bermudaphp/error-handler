<?php

namespace Bermuda\ErrorHandler;

use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Psr\Log\LoggerInterface;

/**
 * Class LogErrorListener
 * @package Bermuda\ErrorHandler
 */
final class LogErrorListener implements ErrorListenerInterface
{
    private LoggerInterface $logger;
    private ErrorRendererInterface $renderer;
    private array $except = [];

    public function __construct(LoggerInterface $logger, ErrorRendererInterface $renderer = null)
    {
        $this->logger = $logger;
        $this->renderer = $renderer ?? WhoopsRenderer::plainTextRendering();
    }

    public function except(string $errorType): self
    {
        $this->except[] = $errorType;
        return $this;
    }

    /**
     * @param ErrorEvent $event
     */
    public function __invoke(ErrorEvent $event): void
    {
        if (!in_array($this->getExceptionClass($event), $this->except))
        {
            $this->logger->error($this->renderer->renderException($event->getThrowable()));
        }
    }

    private function getExceptionClass(ErrorEvent $event): string
    {
        return get_class($event->getThrowable() instanceof HttpException ?
            $event->getThrowable()->getPrevious() : $event->getThrowable()
        );
    }
}
