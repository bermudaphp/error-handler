<?php

namespace Bermuda\ErrorHandler\Generator;

use Whoops\Run;
use Whoops\RunInterface;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PrettyPageHandler;
use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;

class WhoopsErrorGenerator implements ErrorResponseGeneratorInterface
{
    private RunInterface $whoops;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, RunInterface $whoops = null)
    {
        $this->setWhoops($whoops)->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function generateResponse(Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        ($response = $this->responseFactory->createResponse($e->getCode()))
            ->getBody()->write($this->renderException($e));

        return $response;
    }

    protected function renderException(ServerException $e): string
    {
        foreach ($this->whoops->getHandlers() as $handler) {
            $this->addRequestInformation($handler, $e->getServerRequest());
        }

        return $this->whoops->handleException($e->getPrevious());
    }

    /**
     * @param RunInterface|null $whoops
     * @return $this
     */
    protected function setWhoops(?RunInterface $whoops): self
    {
        if (!$whoops) {

            $whoops = new Run();

            foreach ($this->getHandlers() as $handler) {
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
    protected function addRequestInformation($handler, ServerRequestInterface $request): void
    {
    }

    /**
     * @return HandlerInterface[]
     */
    protected function getHandlers(): iterable
    {
        yield new PrettyPageHandler();
    }
}
