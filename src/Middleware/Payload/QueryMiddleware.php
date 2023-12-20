<?php

namespace Muscobytes\HttpClient\Middleware\Payload;

use Http\Discovery\Psr17FactoryDiscovery;
use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class QueryMiddleware implements MiddlewareInterface
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
                http_build_query($this->payload)
            )
        );
    }
}
