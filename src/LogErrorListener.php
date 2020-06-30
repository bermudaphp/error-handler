<?php


namespace Bermuda\ErrorHandler;


use Psr\Log\LoggerInterface;


/**
 * Class LoggerListener
 * @package Bermuda\ErrorHandler
 */
class LogErrorListener implements ErrorListener
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
        $this->logger->error(sprintf('%d [%s] %s: %s', $event->getResponse()->getStatusCode(), $req = $event->getRequest()->getMethod(), (string) $req->getUri(), $event->getThrowable()->getMessage()));
    }
}
