<?php

namespace Muscobytes\HttpClient\Tests\Middleware\Payload;

use Http\Discovery\Psr17FactoryDiscovery;
use Muscobytes\HttpClient\Middleware\Payload\JsonMiddleware;
use Muscobytes\HttpClient\Middleware\Payload\QueryMiddleware;
use Muscobytes\HttpClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;

#[CoversClass(QueryMiddleware::class)]
class QueryMiddlewareTest extends TestCase
{
    public function testIfConstructorProperlySetsPayloadAndStreamFactory()
    {
        $payload = ['foo' => 'bar'];
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        $object = new QueryMiddleware($payload, $streamFactory);
        $this->assertObjectHasProperty('payload', $object);
        $this->assertObjectHasProperty('streamFactory', $object);
        $this->assertEquals($payload, self::getPrivateProperty($object, 'payload'));
        $this->assertEquals($streamFactory, self::getPrivateProperty($object, 'streamFactory'));
    }


    /**
     * @throws Exception
     */
    public function testIfProcessMethodProperlySetsJsonEncodedPayload()
    {
        $payload = ['foo' => 'bar', 'bar' => 'foo'];
        $object = new QueryMiddleware($payload);
        $request = $this->createMock('Psr\Http\Message\RequestInterface');
        $request->expects($this->once())
            ->method('withBody')
            ->with(
                $this->callback(
                    function ($subject) use ($payload) {
                        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $subject);
                        $this->assertEquals(http_build_query($payload), $subject->getContents());
                        return true;
                    }
                )
            )
            ->willReturnSelf();
        $object->process($request);
    }
}
