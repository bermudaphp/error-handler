<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Whoops\Run;
use Whoops\RunInterface;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PrettyPageHandler;
use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Renderer\WhoopsRenderer;
use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;
use function Bermuda\ErrorHandler\get_error_code;

final class WhoopsErrorGenerator implements ErrorResponseGeneratorInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory, private WhoopsRenderer $whoops = new WhoopsRenderer) {
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
        $renderer = $this->renderer;
        
        if ($request != null && $renderer instanceof ServerRequestAwareInterface) {
            ($renderer = clone $renderer)->setServerRequest($request);
        }
        
        $response = $this->responseFactory->createResponse(get_error_code($e->getCode()));
        $response->getBody()->write($renderer->renderException($e));
        
        return $response;
    }
}
