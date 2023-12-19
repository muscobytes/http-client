<?php

namespace Muscobytes\HttpClient\Tests;

use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Muscobytes\HttpClient\Middleware\ContentTypeMiddleware;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use ReflectionException;

#[CoversClass(ContentTypeMiddleware::class)]
class ContentTypeMiddlewareTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    #[DataProvider('contentTypeDataProvider')]
    public function testIfConstructorProperlySetsContentTypeString(string $userAgent)
    {
        $object = new ContentTypeMiddleware($userAgent);
        $this->assertInstanceOf(MiddlewareInterface::class, $object);
        $this->assertObjectHasProperty('contentType', $object);
        $this->assertEquals($userAgent, self::getPrivateProperty($object, 'contentType'));
    }


    /**
     * @throws Exception
     */
    #[DataProvider('contentTypeDataProvider')]
    public function testIfProcessMethodProperlySetsContentTypeHeader(string $contentType)
    {
        $object = new ContentTypeMiddleware($contentType);
        $request = $this->createMock('Psr\Http\Message\RequestInterface');
        $request->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', $contentType)
            ->willReturnSelf();
        $object->process($request);
    }


    public static function contentTypeDataProvider(): array
    {
        return [
            ['application/java-archive'],
            ['application/javascript'],
            ['application/octet-stream'],
            ['application/xhtml+xml'],
            ['application/x-shockwave-flash'],
            ['application/json'],
            ['application/ld+json'],
            ['application/xml'],
            ['application/zip'],
            ['application/x-www-form-urlencoded'],
        ];
    }
}