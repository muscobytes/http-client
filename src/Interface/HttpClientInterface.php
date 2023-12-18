<?php

namespace Muscobytes\HttpClient\Interface;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function request(
        string $method,
        string $uri,
        MiddlewareInterface ...$middlewares
    ): ResponseInterface;

}