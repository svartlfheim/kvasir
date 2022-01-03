<?php

namespace App\Tests\Unit\Connections\Handler\Response;

use App\Common\API\HTTPResponseBuilder;
use App\Connections\API\ConnectionSerializer;
use App\Connections\Handler\Response\ConnectionResponseInterface;
use App\Connections\Handler\Response\Factory;
use App\Tests\Unit\Connections\Handler\Response\Stubs\EmptyInterface1;
use App\Tests\Unit\Connections\Handler\Response\Stubs\EmptyInterface2;
use App\Tests\Unit\TestCase;
use InvalidArgumentException;

class FactoryTest extends TestCase
{
    public function testMakesClass(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $f = new Factory($mockSerializer);

        $testObj = new class ($mockSerializer, $mockResponseBuilder) implements ConnectionResponseInterface {
            public $serializer;
            public $httpResponseBuilder;

            public function __construct(ConnectionSerializer $serializer, HTTPResponseBuilder $httpResponseBuilder)
            {
                $this->serializer = $serializer;
                $this->httpResponseBuilder = $httpResponseBuilder;
            }
        };

        $res = $f->make($testObj::class);

        $this->assertSame($mockSerializer, $res->serializer);
        // It should've passed a fresh instance, the mock was used to create the anonymous class only
        $this->assertNotSame($mockResponseBuilder, $res->httpResponseBuilder);
        $this->assertInstanceOf(HTTPResponseBuilder::class, $res->httpResponseBuilder);
    }

    public function testThrowsExceptionIfNoInterfacesAreImplemented(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);

        $f = new Factory($mockSerializer);

        $testObj = new class () {};

        $expectClass = $testObj::class;
        $this->expectExceptionObject(new InvalidArgumentException("Class '$expectClass' must implement " . ConnectionResponseInterface::class));
        $f->make($testObj::class);
    }

    public function testThrowsExceptionIfWrongInterfacesAreImplemented(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);

        $f = new Factory($mockSerializer);

        $testObj = new class () implements EmptyInterface1, EmptyInterface2 {};

        $expectClass = $testObj::class;
        $this->expectExceptionObject(new InvalidArgumentException("Class '$expectClass' must implement " . ConnectionResponseInterface::class));
        $f->make($testObj::class);
    }
}
