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
    
    public function canGenerate(Throwable $e, ServerRequestInterface $request = null): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function generateResponse(Throwable $e, ServerRequestInterface $request = null): ResponseInterface
    {
        $code = get_error_code($e->getCode());
        ($response = $this->responseFactory->createResponse($code)->withHeader('Content-Type', 'text/html'))
            ->getBody()->write(($this->templateRenderer)($code, $request));
        
        return $response;
    }
}
