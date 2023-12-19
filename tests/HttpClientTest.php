<?php

namespace Muscobytes\HttpClient\Tests;

use Generator;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Muscobytes\HttpClient\Exception\ClientException;
use Muscobytes\HttpClient\Exception\ServerErrorException;
use Muscobytes\HttpClient\Exception\ServiceUnavailableException;
use Muscobytes\HttpClient\Exception\UnknownErrorException;
use Muscobytes\HttpClient\HttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;


#[CoversClass(HttpClient::class)]
class HttpClientTest extends TestCase
{
    public function createHttpClient(ResponseInterface $response): HttpClient
    {
        $client = new Client();
        $client->addResponse($response);

        return new HttpClient(
            $client,
            Psr17FactoryDiscovery::findRequestFactory()
        );
    }


    /**
     * @throws ServiceUnavailableException
     * @throws UnknownErrorException
     * @throws ClientExceptionInterface
     * @throws ClientException
     * @throws ServerErrorException
     */
    public function testSuccessfulRequest()
    {
        $mockResponse = Psr17FactoryDiscovery::findResponseFactory()
            ->createResponse(200);
        $response = $this->createHttpClient($mockResponse)
            ->request('GET', 'https://httpbin.org/get');
        $this->assertSame($response, $mockResponse);
        $this->assertSame(200, $response->getStatusCode());
    }


    /**
     * @throws ServiceUnavailableException
     * @throws ClientExceptionInterface
     * @throws UnknownErrorException
     * @throws ClientException
     * @throws ServerErrorException
     */
    #[DataProvider('ifRequestMethodThrowsExceptionsDataProvider')]
    public function testIfRequestMethodThrowsExceptions($code, $exceptionClass)
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionCode($code);
        $this->createHttpClient(
            Psr17FactoryDiscovery::findResponseFactory()->createResponse($code)
        )->request('GET', 'https://httpbin.org/get');
    }


    public static function ifRequestMethodThrowsExceptionsDataProvider(): Generator
    {
        $namespace = 'Muscobytes\\HttpClient\\Exception\\';
        $exceptions = [
            'ClientException' => [400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411,
                412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 426, 428, 429, 431, 451],
            'ServiceUnavailableException' => [501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511],
            'ServerErrorException' => [500],
            'UnknownErrorException' => [0, -1, 1000],
        ];
        foreach ($exceptions as $exception => $codes) {
            foreach ($codes as $code) {
                yield [$code, $namespace . $exception];
            }
        }
    }


    #[DataProvider('ifShorthandMethodsAvailableDataProvider')]
    public function testIfShorthandMethodsAvailable(string $method)
    {
        $mockResponse = Psr17FactoryDiscovery::findResponseFactory()
            ->createResponse(200);
        $response = $this->createHttpClient($mockResponse)->{$method}('https://httpbin.org/get');
        $this->assertSame($response, $mockResponse);
        $this->assertSame(200, $response->getStatusCode());
    }


    public static function ifShorthandMethodsAvailableDataProvider(): array
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
            ['OPTIONS'],
            ['HEAD']
        ];
    }


    public function testIfIllegalMethodCallThrowsException()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Method FOO is not allowed');
        $this->createHttpClient(
            Psr17FactoryDiscovery::findResponseFactory()->createResponse(200)
        )->FOO('https://httpbin.org/get');
    }
}
