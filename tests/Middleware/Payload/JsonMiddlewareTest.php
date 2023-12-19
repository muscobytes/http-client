<?php

namespace Muscobytes\HttpClient\Tests\Middleware\Payload;

use Http\Discovery\Psr17FactoryDiscovery;
use Muscobytes\HttpClient\Middleware\Payload\JsonMiddleware;
use Muscobytes\HttpClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;

#[CoversClass(JsonMiddleware::class)]
class JsonMiddlewareTest extends TestCase
{
    public function testIfConstructorProperlySetsPayloadAndStreamFactory()
    {
        $payload = ['foo' => 'bar'];
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        $object = new JsonMiddleware($payload, $streamFactory);
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
        $payload = ['foo' => 'bar'];
        $object = new JsonMiddleware($payload);
        $request = $this->createMock('Psr\Http\Message\RequestInterface');
        $request->expects($this->once())
            ->method('withBody')
            ->with(
                $this->callback(
                    function ($subject) use ($payload) {
                        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $subject);
                        $this->assertEquals(json_encode($payload), $subject->getContents());
                        return true;
                    }
                )
            )
            ->willReturnSelf();
        $object->process($request);
    }
}