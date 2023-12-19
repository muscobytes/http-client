<?php

namespace Muscobytes\HttpClient\Tests\Middleware;

use Muscobytes\HttpClient\Interface\MiddlewareInterface;
use Muscobytes\HttpClient\Middleware\UserAgentMiddleware;
use Muscobytes\HttpClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use ReflectionException;

#[CoversClass(UserAgentMiddleware::class)]
class UserAgentMiddlewareTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    #[DataProvider('userAgentDataProvider')]
    public function testIfConstructorProperlySetsUserAgentString(string $userAgent)
    {
        $object = new UserAgentMiddleware($userAgent);
        $this->assertInstanceOf(MiddlewareInterface::class, $object);
        $this->assertObjectHasProperty('userAgent', $object);
        $this->assertEquals($userAgent, self::getPrivateProperty($object, 'userAgent'));
    }


    /**
     * @throws Exception
     */
    #[DataProvider('userAgentDataProvider')]
    public function testIfProcessMethodProperlySetsUserAgentHeader(string $userAgent)
    {
        $object = new UserAgentMiddleware($userAgent);
        $request = $this->createMock('Psr\Http\Message\RequestInterface');
        $request->expects($this->once())
            ->method('withHeader')
            ->with('User-Agent', $userAgent)
            ->willReturnSelf();
        $object->process($request);
    }


    public static function userAgentDataProvider(): array
    {
        return [
            ['Sanity is a matter of consensus'],
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36'],
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12A365 Safari/600.1.4'],
            ['Mozilla/5.0 (Linux; Android 4.4.3; KFAPWI Build/KTU84M) AppleWebKit/537.36 (KHTML, like Gecko) Silk/44.1.81 like Chrome/44.0.2403.128 Safari/537.36']
        ];
    }
}