<?php


namespace Bermuda\ErrorHandler;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;


/**
 * Class TemplateErrorGenerator
 * @package Bermuda\ErrorHandler
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
        
        $this->templateRenderer = static function ($code) use($templateRenderer): string
        {
            return $templateRenderer($code);
        };
    }

    /**
     * @param \Throwable $e
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function generate(\Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->factory->createResponse($code = status_code($e));
        $response = $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(($this->templateRenderer)($code));

        return $response;
    }
}
