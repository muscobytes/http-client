<?php

namespace Muscobytes\HttpClient\Middleware;

use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;


class UserAgentMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $userAgent
    )
    {
        //
    }

    public function process(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('User-Agent', $this->userAgent);
    }
}