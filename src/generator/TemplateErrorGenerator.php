<?php

namespace Bermuda\ErrorHandler\Generator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bermuda\ErrorHandler\RequestHandlingException;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;

/**
 * Class TemplateErrorGenerator
 * @package Bermuda\ErrorHandler\Generator
 */
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
        $this->templateRenderer = static fn ($code): string => return $templateRenderer($code);
    }

    /**
     * @inheritDoc
     */
    public function generate(RequestHandlingException $e): ResponseInterface
    {
        ($response = $this->factory->createResponse($e->getCode())
            ->withHeader('Content-Type', 'text/html'))
            ->getBody()->write(($this->templateRenderer)($e->getCode()))

        return $response;
    }
}
