<?php

namespace Muscobytes\HttpClient\Middleware;

use Http\Discovery\Psr17FactoryDiscovery;
use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Json implements MiddlewareInterface
{
    public function __construct(
        private readonly array $payload = [],
        private ?StreamFactoryInterface $streamFactory = null
    )
    {
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }


    public function process(RequestInterface $request): RequestInterface
    {
        return $request->withBody(
            $this->streamFactory->createStream(
                json_encode($this->payload)
            )
        );
    }
}