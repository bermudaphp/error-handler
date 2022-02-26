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
use Bermuda\HTTP\ServerRequestAwareInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;
use function Bermuda\ErrorHandler\get_error_code;

final class WhoopsErrorGenerator implements ErrorResponseGeneratorInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory, private WhoopsRenderer $whoops = new WhoopsRenderer) {
    }

    /**
     * @inheritDoc
     */
    public function generateResponse(Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $renderer = clone $this->renderer;
        
        if ($renderer instanceof ServerRequestAwareInterface) {
            $renderer->setServerRequest($request);
        }
        
        $response = $this->responseFactory->createResponse(get_error_code($e->getCode()));
        $response->getBody()->write($renderer->renderException($e));
        
        return $response;
    }
}
