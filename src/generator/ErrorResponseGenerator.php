<?php

namespace Bermuda\ErrorHandler\Generator;

use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;
use Bermuda\HTTP\Contracts\ResponseFactoryAwareInterface;

final class ErrorResponseGenerator implements ErrorResponseGeneratorInterface, ResponseFactoryAwareInterface, ServerRequestAwareInterface
{
    private array $generators = [];
    private ServerRequestInterface $serverRequest;
    public function __construct(private ResponseFactoryInterface $responseFactory, private WhoopsErrorGenerator $whoopsErrorGenerator) {
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ServerRequestAwareInterface
     */
    public function setServerRequest(ServerRequestInterface $serverRequest): ServerRequestAwareInterface
    {
        $this->serverRequest = $serverRequest;
        foreach ($this->generators as $generator) {
            if ($generator instanceof ServerRequestAwareInterface) {
                $generator->setServerRequest($serverRequest);
            }
        }
        return $this;
    }

    /**
     * @param ResponseFactoryInterface $factory
     * @return ResponseFactoryAwareInterface
     */
    public function setResponseFactory(ResponseFactoryInterface $factory): ResponseFactoryAwareInterface
    {
        $this->pesponseFactory = $factory;
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

    public function canGenerate(Throwable $e): bool
    {
        return true;
    }

    public function generateResponse(Throwable $e): ResponseInterface
    {
        foreach ($this->generators as $generator) {
            if ($generator->canGenerate($e)) {
                return $generator->generateResponse($e);
            }
        }
        
        return $this->whoopsErrorGenerator->generateResponse($e);
    }
}
