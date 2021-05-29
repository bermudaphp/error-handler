<?php

namespace Bermuda\ErrorHandler;

use Psr\Log\LoggerInterface;

/**
 * Class LogErrorListener
 * @package Bermuda\ErrorHandler
 */
final class LogErrorListener implements ErrorListenerInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ErrorEvent $event
     */
    public function __invoke(ErrorEvent $event): void
    {
        if ($event instanceof HttpErrorEvent)
        {
            $this->logger->error(
                sprintf('%d [%s] %s: %s', 
                        $event->getResponse()->getStatusCode(),
                        $req = $event->getRequest()->getMethod(), 
                        (string) $req->getUri(), $event->getThrowable()
                            ->getMessage()
                       )
            );
            
            return;
        }
        
        $this->logger->error($event->getThrowable()->getMessage());
    }
}
