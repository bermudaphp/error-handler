<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;
use Bermuda\HTTP\Contracts\ResponseFactoryAwareInterface;

final class ErrorResponseGenerator implements ErrorResponseGeneratorInterface, ResponseFactoryAwareInterface
{
    private array $generators = [];
    public function __construct(private ResponseFactoryInterface $factory, private WhoopsErrorGenerator $whoopsErrorGenerator) {
    }

    /**
     * @param ResponseFactoryInterface $factory
     * @return ResponseFactoryAwareInterface
     */
    public function setResponseFactory(ResponseFactoryInterface $factory): ResponseFactoryAwareInterface
    {
        $this->factory = $factory;
        
        foreach ($this->generators as $generator) {
            if ($generator instanceof ResponseFactoryAwareInterface) {
                $generator->setResponseFactory($factory);
            }
        }
        
        return $this;
    }

    public function addGenerator(ErrorResponseGeneratorInterface $generator): self
    {
        if ($generator instanceof ResponseFactoryAwareInterface) {
            $generator->setResponseFactory($this->responseFactory);
        }
        
        array_unshift($this->generators, $generator);
        return $this;
    }

    public function hasGenerator(string|ErrorResponseGeneratorInterface $generator): bool
    {
        foreach ($this->generators as $g) {
            if ($g::class == is_string($generator) ? $generator : $generator::class) {
                return true;
            }
        }
        
        return false;
    }

    public function canGenerate(Throwable $e, ServerRequestInterface $request = null): bool
    {
        return true;
    }

    public function generateResponse(Throwable $e, ServerRequestInterface $request = null): ResponseInterface
    {
        foreach ($this->generators as $generator) {
            if ($generator->canGenerate($e, $request)) {
                return $generator->generateResponse($e, $request);
            }
        }
        
        return $this->whoopsErrorGenerator->generateResponse($e, $request);
    }
}
