<?php
declare(strict_types=1);

namespace Muscobytes\HttpClient\Interface;

use Psr\Http\Message\RequestInterface;

interface MiddlewareInterface
{
    public function process(
        RequestInterface $request,
    ): RequestInterface;
}
