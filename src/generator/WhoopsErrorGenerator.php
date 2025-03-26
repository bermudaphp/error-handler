<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Bermuda\HTTP\Contracts\ServerRequestAwareTrait;
use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;

use function Bermuda\ErrorHandler\getErrorCode;

final class WhoopsErrorGenerator implements ErrorResponseGeneratorInterface, ServerRequestAwareInterface
{
    use ServerRequestAwareTrait;

    public function __construct(
        private ResponseFactoryInterface $responseFactory, 
        private WhoopsRenderer $renderer = new WhoopsRenderer
    ) {
    }

    public function canGenerate(Throwable $e): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function generateResponse(Throwable $e): ResponseInterface
    {
        $request = $e?->serverRequest ?? $this->serverRequest;
        
        if ($request !== null && $this->renderer instanceof ServerRequestAwareInterface) {
            ($renderer = clone $this->renderer)->setServerRequest($request);
        }

        $response = $this->responseFactory->createResponse(getErrorCode($e));
        $response->getBody()->write($renderer->renderException($e));
        
        return $response;
    }

    public static function createFromContainer(ContainerInterface $container): self
    {
        return new self($container->get(ResponseFactoryInterface::class));
    }
}
