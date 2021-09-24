<?php

namespace Bermuda\ErrorHandler\Generator;

use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;

final class TemplateErrorGenerator implements ErrorResponseGeneratorInterface
{
    /**
     * @var callable
     */
    private $templateRenderer;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(callable $templateRenderer, ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->templateRenderer = static fn($code): string => $templateRenderer($code);
    }

    /**
     * @inheritDoc
     */
    public function generate(ServerException $e): ResponseInterface
    {
        ($response = $this->responseFactory->createResponse($e->getCode())
            ->withHeader('Content-Type', 'text/html'))
            ->getBody()->write(($this->templateRenderer)($e->getCode()));

        return $response;
    }
}
