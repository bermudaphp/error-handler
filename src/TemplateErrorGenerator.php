<?php


namespace Lobster;


use Lobster\Contracts\ErrorResponseGenerator;
use Lobster\Contracts\Renderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Class TemplateErrorGenerator
 * @package Lobster
 */
class TemplateErrorGenerator implements ErrorResponseGenerator
{
    private Renderer $renderer;
    private ResponseFactoryInterface $factory;

    /**
     * TemplateErrorGenerator constructor.
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer, ResponseFactoryInterface $factory)
    {
        $this->renderer = $renderer;
        $this->factory = $factory;
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
        $response->getBody()->write($this->renderer->render($this->getTemplate($code)));

        return $response;
    }

    /**
     * @param int $code
     * @return string
     */
    protected function getTemplate(int $code) : string
    {
        return 'error::' . $code ;
    }
}