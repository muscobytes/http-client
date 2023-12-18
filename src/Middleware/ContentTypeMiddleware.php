<?php

namespace Muscobytes\HttpClient\Middleware;

use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;

class ContentTypeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $contentType
    )
    {
        //
    }

    public function process(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Content-Type', $this->contentType);
    }
}