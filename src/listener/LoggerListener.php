<?php

namespace Bermuda\ErrorHandler\Listener;

use Psr\Log\LoggerInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Bermuda\ErrorHandler\Renderer\ErrorRendererInterface;

final class LoggerListener implements ErrorListenerInterface
{
    private array $except = [];

    public function __construct(
        private LoggerInterface $logger,
        private ErrorRendererInterface $renderer = new WhoopsRenderer,
        public readonly int $priority = 1
    ) {
    }

    public function except(string $errorType): self
    {
        $this->except[] = $errorType;
        return $this;
    }

    /**
     * @param ErrorEvent $event
     */
    public function handleEvent(ErrorEvent $event): void
    {
        if (!in_array($event->exception::class, $this->except)) {
            $this->logger->error($this->renderer->renderException($event->exception));
        }
    }
}
