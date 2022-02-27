<?php

namespace Bermuda\ErrorHandler;

use Psr\Log\LoggerInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;

final class LogErrorListener implements ErrorListenerInterface
{
    use ErrorListener;
    private array $except = [];
    public function __construct(private LoggerInterface $logger, private ErrorRendererInterface $renderer = new WhoopsRenderer, private int $priority = 1) {
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
        if (!in_array($event->throwable::class, $this->except)) {
            $this->logger->error($this->renderer->renderException($event->throwable));
        }
    }
}
