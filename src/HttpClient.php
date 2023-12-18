<?php
declare(strict_types=1);

namespace Muscobytes\HttpClient;

use Muscobytes\HttpClient\Interface\HttpClientInterface;
use Muscobytes\HttpClinet\Exception\ClientException;
use Muscobytes\HttpClinet\Exception\ServerErrorException;
use Muscobytes\HttpClinet\Exception\ServiceUnavailableException;
use Muscobytes\HttpClinet\Exception\UnknownException;
use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;


final class HttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory
    )
    {
        //
    }


    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws ServerErrorException
     * @throws ServiceUnavailableException
     * @throws UnknownException
     */
    public function request(
        string $method,
        string $uri,
        MiddlewareInterface ...$middlewares
    ): ResponseInterface
    {
        $request = $this->requestFactory->createRequest($method, $uri);

        foreach ($middlewares as $middleware) {
            $request = $middleware->process($request);
        }

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() > 500) {
            throw new ServiceUnavailableException($response->getReasonPhrase(), $response->getStatusCode());
        } elseif ($response->getStatusCode() === 500) {
            throw new ServerErrorException($response->getReasonPhrase(), $response->getStatusCode());
        } elseif ($response->getStatusCode() >= 400) {
            throw new ClientException($response->getReasonPhrase(), $response->getStatusCode());
        } elseif ($response->getStatusCode() !== 200) {
            throw new UnknownException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response;
    }


    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws ServerErrorException
     * @throws ServiceUnavailableException
     * @throws UnknownException
     */
    public function __call(string $name, array $arguments): ResponseInterface
    {
        $allowed = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];
        if (!in_array($name, $allowed)) {
            throw new ClientException("Method $name is not allowed");
        }
        return $this->request($name, ...$arguments);
    }
}
