<?php

namespace Bermuda\ErrorHandler\Generator;

use Psr\Http\Message\ResponseInterface;
use Bermuda\ErrorHandler\ServerException;
use Bermuda\ErrorHandler\ErrorResponseGeneratorInterface;

final class AggregateErrorResponseGenerator implements ErrorResponseGeneratorInterface
{
    /**
     * @var ErrorResponseGeneratorInterface[] 
     */
    private array $generators = [];
    private ErrorResponseGeneratorInterface $fallbackGenerator;

    public function __construct(ErrorResponseGeneratorInterface $fallbackGenerator, iterable $generators = [])
    {
        $this->fallbackGenerator = $fallbackGenerator;
        foreach ($generators as $exceptionCls => $generator) {
            $this->addGenerator($exceptionCls, $generator);
        }
    }

    /**
     * @param string $exceptionCls
     * @param ErrorResponseGeneratorInterface $generator
     * @return self
     */
    public function addGenerator(string $exceptionCls, ErrorResponseGeneratorInterface $generator): self
    {
        $this->generators[$exceptionCls] = $generator;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generate(ServerException $e): ResponseInterface
    {
        if ($this->generators[$cls = get_class($e->getPrevious())]) {
            return $this->generators[$cls]->generate($e);
        }
        
        return $this->fallbackGenerator->generate($e);
    }
}
