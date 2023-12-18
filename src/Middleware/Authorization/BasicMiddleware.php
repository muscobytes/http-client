<?php
declare(strict_types=1);

namespace Muscobytes\HttpClient\Middleware;

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


    private function getBase64Credentials(): string
    {
        return base64_encode($this->username . ':' . $this->password);
    }


    public function process(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', 'Basic ' . $this->getBase64Credentials());
    }
}