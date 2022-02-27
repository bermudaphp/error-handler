<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;

final class ErrorResponseGenerator implements ErrorResponseGeneratorInterface
{
    private array $generators = [];
    public function __construct(private WhoopsErrorGenerator $whoopsErrorGenerator) {
    }

    public function addGenerator(ErrorResponseGeneratorInterface $generator): self
    {
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
