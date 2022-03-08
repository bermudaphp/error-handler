<?php

namespace Bermuda\ErrorHandler\Generator;

use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;
use Bermuda\Router\Exception\MethodNotAllowedException;
use Throwable;
use function Bermuda\ErrorHandler\get_error_code;

final class TemplateErrorGenerator implements ErrorResponseGeneratorInterface
{
    /**
     * @var callable
     */
    private $templateRenderer;
    public function __construct(callable $templateRenderer, private ResponseFactoryInterface $responseFactory)
    {
        $this->templateRenderer = static fn($code, ServerRequestInterface $req = null): string => $templateRenderer($code, $req);
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
        $code = get_error_code($e);
        ($response = $this->responseFactory->createResponse($code)->withHeader('Content-Type', 'text/html'))
            ->getBody()->write(($this->templateRenderer)($code, $e?->serverRequest));
        
        return $response;
    }
}
