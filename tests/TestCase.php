<?php

namespace Muscobytes\HttpClient\Tests;

use ReflectionException;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }


    // https://www.yellowduck.be/posts/another-way-of-accessing-private-and-protected-properties-in-php
    public static function getPrivateProperty($object, $property)
    {
        return (fn () => $this->{$property})->call($object);
    }
}