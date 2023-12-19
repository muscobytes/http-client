<?php

namespace Muscobytes\HttpClient\Tests;

use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Muscobytes\HttpClient\Middleware\Authorization\BasicMiddleware;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;

#[CoversClass(BasicMiddleware::class)]
class BasicMiddlewareTest extends TestCase
{
    #[DataProvider('basicAuthorizationDataProvider')]
    public function testIfConstructorProperlySetsContentTypeString(string $username, string $password, string $expected)
    {
        $object = new BasicMiddleware($username, $password);
        $this->assertInstanceOf(MiddlewareInterface::class, $object);
        $this->assertObjectHasProperty('username', $object);
        $this->assertObjectHasProperty('password', $object);
        $this->assertEquals($username, self::getPrivateProperty($object, 'username'));
        $this->assertEquals($password, self::getPrivateProperty($object, 'password'));
    }


    /**
     * @throws Exception
     */
    #[DataProvider('basicAuthorizationDataProvider')]
    public function testIfProcessMethodProperlySetsContentTypeHeader(string $username, string $password, string $expected)
    {
        $object = new BasicMiddleware($username, $password);
        $request = $this->createMock('Psr\Http\Message\RequestInterface');
        $request->expects($this->once())
            ->method('withHeader')
            ->with('Authorization', 'Basic ' . $expected)
            ->willReturnSelf();
        $object->process($request);
    }


    public static function basicAuthorizationDataProvider(): array
    {
        return [
            ['root', 'password', 'cm9vdDpwYXNzd29yZA=='],
            ['', '', 'Og=='],
        ];
    }
}