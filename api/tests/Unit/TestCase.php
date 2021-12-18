<?php

namespace App\Tests\Unit;

use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /*
    It's quite common, that we'll end up needing some shared functionality for all tests
    The easiest way to do that is to extend the base test case
    This is here so we don't have to go though mass amounts of refactoring when we need this
    If we never need extra functionality, we haven't really lost much by having this here.
    */

    protected function buildMockIterator(string $class, array $items): MockObject
    {
        $iter = $this->createMock($class);

        $iterator = new ArrayIterator($items);

        $iter->method('rewind')
            ->willReturnCallback(function () use ($iterator): void {
                $iterator->rewind();
            });

        $iter->method('current')
            ->willReturnCallback(function () use ($iterator) {
                return $iterator->current();
            });

        $iter->method('key')
            ->willReturnCallback(function () use ($iterator) {
                return $iterator->key();
            });

        $iter->method('next')
            ->willReturnCallback(function () use ($iterator): void {
                $iterator->next();
            });

        $iter->method('valid')
            ->willReturnCallback(function () use ($iterator): bool {
                return $iterator->valid();
            });

        return $iter;
    }
}
