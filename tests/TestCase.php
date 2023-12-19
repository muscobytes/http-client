<?php

namespace Muscobytes\HttpClient\Tests;

use ReflectionException;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }


    /**
     * @throws ReflectionException
     */
    public static function getPrivateProperty($object, $property)
    {
        $reflectedClass = new \ReflectionClass($object);
        $reflection = $reflectedClass->getProperty($property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }
}