<?php

namespace Bermuda\ErrorHandler\Generator;

use Throwable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\ConfigProvider;
use Bermuda\HTTP\Contracts\ServerRequestAwareInterface;
use Bermuda\HTTP\Contracts\ResponseFactoryAwareInterface;

use function Bermuda\Config\conf;

final class ErrorResponseGenerator implements ErrorResponseGeneratorInterface, ResponseFactoryAwareInterface, ServerRequestAwareInterface
{

    /**
     * @var ErrorResponseGeneratorInterface[]
     */
    private array $generators = [];
    private ?ServerRequestInterface $serverRequest = null;

    public function __construct(
        private ResponseFactoryInterface $responseFactory, 
        private ?WhoopsErrorGenerator $whoopsErrorGenerator = null
    ) {
        if (!$whoopsErrorGenerator) $this->whoopsErrorGenerator = new WhoopsErrorGenerator($responseFactory);
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

        $this->whoopsErrorGenerator->setServerRequest($serverRequest);
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
            if ($generator instanceof ResponseFactoryAwareInterface) $generator->setResponseFactory($factory);
        }
        
        return $this;
    }

    public function addGenerator(ErrorResponseGeneratorInterface $generator): self
    {
        if ($generator instanceof ResponseFactoryAwareInterface) {
            $generator->setResponseFactory($this->responseFactory);
        }
        
        if ($this->serverRequest != null && $generator instanceof ServerRequestAwareInterface) {
            $generator->setServerRequest($this->serverRequest);
        }
        
        $this->generators[$generator::class] = $generator;
        return $this;
    }

    public function hasGenerator(string|ErrorResponseGeneratorInterface $generator): bool
    {
        return isset($this->generators[is_string($generator) ? $generator : $generator::class]);
    }

    public function canGenerate(Throwable $e): bool
    {
        return true;
    }

    public function generateResponse(Throwable $e): ResponseInterface
    {
        foreach ($this->generators as $generator) {
            if ($generator->canGenerate($e)) return $generator->generateResponse($e);
        }
        
        return $this->whoopsErrorGenerator->generateResponse($e);
    }

    public static function createFromContainer(ContainerInterface $container): self
    {
        $generator = new self(
            $container->get(ResponseFactoryInterface::class),
            $container->get(WhoopsErrorGenerator::class)
        );

        $generators = conf($container)->get(ConfigProvider::CONFIG_KEY_GENERATORS, []);
        if (is_iterable($generators)) foreach ($generators as $g) $generator->addGenerator($g);

        return $generator;
    }
}
