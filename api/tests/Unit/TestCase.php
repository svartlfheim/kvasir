<?php

namespace App\Tests\Unit;

use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
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

    protected function buildMockIteratorAggregate(string $class, array $items): MockObject
    {
        $iter = $this->buildMockIterator(ArrayIterator::class, $items);

        $agg = $this->createMock($class);

        $agg->method('getIterator')
            ->willReturn($iter);

        return $agg;
    }
}
