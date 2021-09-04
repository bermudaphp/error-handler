<?php

namespace Bermuda\ErrorHandler\Generator;

use Bermuda\ErrorHandler\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bermuda\ErrorHandler\RequestHandlingException;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;

final class TemplateErrorGenerator implements ErrorResponseGeneratorInterface
{
    /**
     * @var callable
     */
    private $templateRenderer;
    private ResponseFactoryInterface $factory;

    public function __construct(callable $templateRenderer, ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
        $this->templateRenderer = static fn ($code): string => $templateRenderer($code);
    }

    /**
     * @inheritDoc
     */
    public function generate(ServerException $e): ResponseInterface
    {
        ($response = $this->factory->createResponse($e->getCode())
            ->withHeader('Content-Type', 'text/html'))
            ->getBody()->write(($this->templateRenderer)($e->getCode()));

        return $response;
    }
}
