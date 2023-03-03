<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Bermuda\HTTP\Contracts\ServerRequestAwareTrait;
use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;
use function Bermuda\ErrorHandler\get_error_code;

final class WhoopsErrorGenerator implements ErrorResponseGeneratorInterface, ServerRequestAwareInterface
{
    use ServerRequestAwareTrait;
    public function __construct(private ResponseFactoryInterface $responseFactory, private WhoopsRenderer $renderer = new WhoopsRenderer) {
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
        $renderer = $this->renderer;
        $request = $e instanceof ServerException ? $e->serverRequest : $this->serverRequest;
        
        if ($request !== null && $renderer instanceof ServerRequestAwareInterface) {
            ($renderer = clone $renderer)->setServerRequest($request);
        }

        $response = $this->responseFactory->createResponse(get_error_code($e));
        $response->getBody()->write($renderer->renderException($e));
        
        return $response;
    }
}
