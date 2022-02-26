<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class ServerErrorEvent extends ErrorEvent
{
    private ResponseInterface $response;
    private ServerRequestInterface $request;

    public function __construct(Throwable $e, ServerRequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($e);
        $this->request = $request;
        $this->response = $response;
    }
    
    public static function create(ServerException $e, ResponseInterface $response): self
    {
        return new self($e->getPrevious(), $e->getServerRequest(), $response);
    }
    
    /**
     * @param ResponseInterface|null $response
     * @return ResponseInterface
     */
    public function response(?ResponseInterface $response = null): ResponseInterface
    {
        return $response != null ? $this->response = $response : $this->response;
    }

    /**
     * @param ServerRequestInterface|null $request
     * @return ServerRequestInterface|null
     */
    public function request(ServerRequestInterface $request = null): ServerRequestInterface
    {
        return $request != null ? $this->request = $request : $this->request;
    }
}
