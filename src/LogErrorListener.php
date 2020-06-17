<?php


namespace Lobster;


use Psr\Log\LoggerInterface;
use Lobster\Contracts\ErrorEvent;
use Lobster\Contracts\ErrorListener;



/**
 * Class LogErrorListener
 * @package Lobster
 */
class LogErrorListener implements ErrorListener
{
    private string $level;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, string $level = LogLevel::ERROR)
    {
        $this->level = $level;
        $this->logger = $logger;
    }

    /**
     * @param ErrorEvent $event
     */
    public function __invoke(ErrorEvent $event): void
    {
        $this->logger->log($this->level, $event->getError()->getMessage(), ['exception' => $event->getError()]);
    }
}
