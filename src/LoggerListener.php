<?php


namespace Lobster;


use Psr\Log\LoggerInterface;
use Lobster\Contracts\ErrorEvent;
use Lobster\Contracts\ErrorListener;



/**
 * Class LoggerListener
 * @package Lobster
 */
class LoggerListener implements ErrorListener
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
        $this->logger->error(sprintf('%d [%s] %s: %s', $event->getResponse()->getStatusCode(), $req = $event->getRequest()->getMethod(), (string) $req->getUri(), $event->getError()->getMessage()));
    }
}
