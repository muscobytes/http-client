<?php

namespace Muscobytes\HttpClient\Tests\Middleware\Payload;

use Http\Discovery\Psr17FactoryDiscovery;
use Muscobytes\HttpClient\Middleware\Payload\RawMiddleware;
use Muscobytes\HttpClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;

#[CoversClass(RawMiddleware::class)]
class RawMiddlewareTest extends TestCase
{
    public function testIfConstructorProperlySetsPayloadAndStreamFactory()
    {
        $payload = "foo=bar&foo=bar2";
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        $object = new RawMiddleware($payload, $streamFactory);
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
        $payload = "foo=bar&foo=bar2";
        $object = new RawMiddleware($payload);
        $request = $this->createMock('Psr\Http\Message\RequestInterface');
        $request->expects($this->once())
            ->method('withBody')
            ->with(
                $this->callback(
                    function ($subject) use ($payload) {
                        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $subject);
                        $this->assertEquals($payload, $subject->getContents());
                        return true;
                    }
                )
            )
            ->willReturnSelf();
        $object->process($request);
    }
}