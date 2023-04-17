<?php

namespace App\Tests\Unit\Common\Database;

use App\Common\Database\ColumnSortOrder;
use App\Common\Database\SortOrders;
use App\Tests\Unit\TestCase;

class SortOrdersTest extends TestCase
{
    public function testIsIterableAndCountable(): void
    {
        $orders = SortOrders::new(
            ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC),
            ColumnSortOrder::new('otherfield', ColumnSortOrder::DIRECTION_ASC)
        );

        $this->assertEquals(2, count($orders));
        foreach ($orders as $order) {
            $this->assertInstanceOf(ColumnSortOrder::class, $order);
        }
    }

    public function testToArray(): void
    {
        $columnSorts = [
            ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC),
            ColumnSortOrder::new('otherfield', ColumnSortOrder::DIRECTION_ASC)
        ];
        $orders = SortOrders::new(...$columnSorts);

        $this->assertEquals($columnSorts, $orders->toArray());
    }
}
