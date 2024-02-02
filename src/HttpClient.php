<?php
declare(strict_types=1);

namespace Muscobytes\HttpClient;

use Muscobytes\HttpClient\Interface\HttpClientInterface;
use Muscobytes\HttpClient\Exception\ClientException;
use Muscobytes\HttpClient\Exception\ServerErrorException;
use Muscobytes\HttpClient\Exception\ServiceUnavailableException;
use Muscobytes\HttpClient\Exception\UnknownErrorException;
use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


final class HttpClient implements HttpClientInterface
{
    private RequestInterface $request;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory
    )
    {
    }


    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws ServerErrorException
     * @throws ServiceUnavailableException
     * @throws UnknownErrorException
     * @param array<MiddlewareInterface> $middlewares
     */
    public function request(
        string $method,
        string $uri,
        array $middlewares = []
    ): ResponseInterface
    {
        $this->request = $this->requestFactory->createRequest($method, $uri);

        foreach ($middlewares as $middleware) {
            $this->request = $middleware->process($this->request);
        }

        $response = $this->client->sendRequest($this->request);

        if (in_array($response->getStatusCode(), range(501, 511))) {
            throw new ServiceUnavailableException($response->getReasonPhrase(), $response->getStatusCode());
        } elseif ($response->getStatusCode() === 500) {
            throw new ServerErrorException($response->getReasonPhrase(), $response->getStatusCode());
        } elseif (in_array($response->getStatusCode(), range(400, 499))) {
            throw new ClientException($response->getReasonPhrase(), $response->getStatusCode());
        } elseif ($response->getStatusCode() !== 200) {
            throw new UnknownErrorException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response;
    }


    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws ServerErrorException
     * @throws ServiceUnavailableException
     * @throws UnknownErrorException
     */
    public function __call(string $name, array $arguments): ResponseInterface
    {
        $allowed = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];
        if (!in_array(strtoupper($name), $allowed)) {
            throw new ClientException("Method $name is not allowed");
        }
        return $this->request($name, ...$arguments);
    }
}
