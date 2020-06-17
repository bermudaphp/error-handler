<?php


namespace Lobster;


use Whoops\Run;
use Whoops\RunInterface;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PrettyPageHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Lobster\Contracts\ErrorResponseGenerator;
use Psr\Http\Message\ResponseFactoryInterface;



/**
 * Class WhoopsErrorGenerator
 * @package Lobster
 */
class WhoopsErrorGenerator implements ErrorResponseGenerator
{
    private RunInterface $whoops;
    private ResponseFactoryInterface $factory;

    public function __construct(ResponseFactoryInterface $factory, RunInterface $whoops = null)
    {
        $this->setWhoops($whoops)->factory = $factory;
    }

    /**
     * @param \Throwable $e
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function generate(\Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->factory->createResponse(status_code($e));
        $response->getBody()->write($this->handleException($e, $request));

        return $response;
    }

    /**
     * @param \Throwable $e
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function handleException(\Throwable $e, ServerRequestInterface $request) : string
    {
        foreach ($this->whoops->getHandlers() as $handler)
        {
            $this->addRequestInformation($handler, $request);
        }

        return $this->whoops->handleException($e);
    }

    /**
     * @param RunInterface|null $whoops
     * @return $this
     */
    protected function setWhoops(?RunInterface $whoops) : self
    {
        if (!$whoops)
        {
            $whoops = new Run();

            foreach ($this->getHandlers() as $handler)
            {
                $whoops->pushHandler($handler);
            }
        }

        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);

        $this->whoops = $whoops;

        return $this;
    }

    /**
     * @param PrettyPageHandler $handler
     * @param ServerRequestInterface $request
     */
    protected function addRequestInformation($handler, ServerRequestInterface $request) : void
    {
    }

    /**
     * @return HandlerInterface[]
     */
    protected function getHandlers() : iterable
    {
        yield new PrettyPageHandler();
    }
}
