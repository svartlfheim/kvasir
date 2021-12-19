<?php

namespace App\Tests\Unit\Common\ArgumentResolver;

use App\Common\ArgumentResolver\CommandResolver;
use App\Tests\Unit\Common\ArgumentResolver\Stubs\DoesNotImplementFromRequest;
use App\Tests\Unit\Common\ArgumentResolver\Stubs\ImplementsFromRequest;
use App\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CommandResolverTest extends TestCase
{
    public function testSupportsForMatchingClass(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->expects($this->exactly(1))
            ->method('getCurrentRequest')
            ->willReturn($requestMock);
        $argumentMock = $this->createMock(ArgumentMetadata::class);
        $argumentMock->expects($this->exactly(1))
            ->method('getType')
            ->willReturn(ImplementsFromRequest::class);

        $resolver = new CommandResolver($requestStackMock);
        $this->assertTrue(
            $resolver->supports($requestMock, $argumentMock)
        );
    }

    public function testSupportsForNonMatchingClass(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->expects($this->exactly(1))
            ->method('getCurrentRequest')
            ->willReturn($requestMock);
        $argumentMock = $this->createMock(ArgumentMetadata::class);
        $argumentMock->expects($this->exactly(1))
            ->method('getType')
            ->willReturn(DoesNotImplementFromRequest::class);

        $resolver = new CommandResolver($requestStackMock);
        $this->assertFalse(
            $resolver->supports($requestMock, $argumentMock)
        );
    }

    public function testResolve(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->expects($this->exactly(1))
            ->method('getCurrentRequest')
            ->willReturn($requestMock);
        $argumentMock = $this->createMock(ArgumentMetadata::class);
        $argumentMock->expects($this->exactly(1))
            ->method('getType')
            ->willReturn(ImplementsFromRequest::class);

        $resolver = new CommandResolver($requestStackMock);
        $iterator = $resolver->resolve($requestMock, $argumentMock);
        $iteratorArray = iterator_to_array($iterator);

        $this->assertEquals(1, count($iteratorArray));
        $this->assertInstanceOf(ImplementsFromRequest::class, $iteratorArray[0]);
    }
}
