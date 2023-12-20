<?php
declare(strict_types=1);

namespace Muscobytes\HttpClient\Middleware\Authentication;

use Http\Message\Authentication\BasicAuth;
use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;

class BasicMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $username,
        private readonly string $password
    )
    {
        //
    }


    public function process(RequestInterface $request): RequestInterface
    {
        return (new BasicAuth($this->username, $this->password))->authenticate($request);
    }
}