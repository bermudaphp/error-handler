<?php

namespace Bermuda\ErrorHandler\Generator;

use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface

final class ResponseErrorGenerator implements ErrorResponseGeneratorInterface
{
    private array $generators = [];
    
    public function __construct(WhoopsErrorGenerator $whoopsErrorGenerator)
    {
        $this->generators[$whoopsErrorGenerator::class] = $whoopsErrorGenerator;
    }
    
    public function addGenerator(ErrorResponseGeneratorInterface $generator): self
    {
        $this->generators[$generator::class] = $generator;
        return $this;
    }
    
    public function hasGenerator(string|ErrorResponseGeneratorInterface $generator): bool
    {
        return isset($this->generators[is_string($generator) ? $generator : $generator::class]);
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
    }
}
