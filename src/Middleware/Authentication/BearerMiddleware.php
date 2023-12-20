<?php
declare(strict_types=1);

namespace Muscobytes\HttpClient\Middleware\Authentication;

use Http\Message\Authentication\Bearer;
use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;

class BearerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $bearer
    )
    {
        //
    }


    public function process(RequestInterface $request): RequestInterface
    {
        return (new Bearer($this->bearer))->authenticate($request);
    }
}