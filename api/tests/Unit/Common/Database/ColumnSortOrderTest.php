<?php

namespace App\Tests\Unit\Common\Database;

use App\Common\Database\ColumnSortOrder;
use App\Tests\Unit\TestCase;
use RuntimeException;

class ColumnSortOrderTest extends TestCase
{
    public function testGetters(): void
    {
        $order = ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC);

        $this->assertEquals('myfield', $order->getField());
        $this->assertEquals(ColumnSortOrder::DIRECTION_ASC, $order->getDirection());
    }

    public function testDirectionGuard(): void
    {
        $this->expectExceptionObject(new RuntimeException("Direction must be one of: ASC, DESC"));
        ColumnSortOrder::new('myfield', 'garbage');
    }
}
