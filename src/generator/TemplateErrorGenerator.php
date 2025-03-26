<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Bermuda\HTTP\Contracts\ServerRequestAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bermuda\Router\Exception\MethodNotAllowedException;

use function Bermuda\ErrorHandler\getErrorCode;

final class TemplateErrorGenerator implements ErrorResponseGeneratorInterface, ServerRequestAwareInterface
{
    use ServerRequestAwareTrait;
    
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
        $code = getErrorCode($e);
        $request = $e?->serverRequest ?? $this->serverRequest;
        ($response = $this->responseFactory->createResponse($code)->withHeader('Content-Type', 'text/html'))
            ->getBody()->write(($this->templateRenderer)($code, $request instanceof ServerRequestInterface ? $request : null));
        
        return $response;
    }
}
