<?php

namespace App\Tests\Unit\Common\Database;

use App\Common\Database\ColumnSortOrder;
use App\Common\Database\ListOptions;
use App\Common\Database\Pagination;
use App\Common\Database\SortOrders;
use App\Tests\Unit\TestCase;

class ListOptionsTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $opts = new ListOptions();

        $this->assertEquals(SortOrders::new(), $opts->getSortOrders());
        $this->assertNull($opts->getPagination());
    }

    public function testMultipleSortOrdersCanBeAdded(): void
    {
        $opts = new ListOptions();

        $sort1 = ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC);
        $sort2 = ColumnSortOrder::new('otherfield', ColumnSortOrder::DIRECTION_DESC);
        $this->assertSame($opts, $opts->addSortOrder($sort1));
        $this->assertSame($opts, $opts->addSortOrder($sort2));

        $this->assertEquals(SortOrders::new($sort1, $sort2), $opts->getSortOrders());
    }

    public function testSortOrdersCanBeSet(): void
    {
        $opts = new ListOptions();

        $sort1 = ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC);
        $sort2 = ColumnSortOrder::new('otherfield', ColumnSortOrder::DIRECTION_DESC);
        $toSet = SortOrders::new($sort1, $sort2);

        $this->assertSame($opts, $opts->setSortOrders($toSet));

        $this->assertEquals($toSet, $opts->getSortOrders());
    }

    public function testSortOrdersCanBeSetAndThenAdded(): void
    {
        $opts = new ListOptions();

        $sort1 = ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC);
        $sort2 = ColumnSortOrder::new('otherfield', ColumnSortOrder::DIRECTION_DESC);
        $sort3 = ColumnSortOrder::new('thirdfield', ColumnSortOrder::DIRECTION_DESC);
        $toSet = SortOrders::new($sort1, $sort2);
        $expected = SortOrders::new($sort1, $sort2, $sort3);

        $this->assertSame($opts, $opts->setSortOrders($toSet));
        $opts->addSortOrder($sort3);

        $this->assertEquals($expected, $opts->getSortOrders());
    }

    public function testSortOrdersCanBeReset(): void
    {
        $opts = new ListOptions();

        $sort1 = ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC);
        $sort2 = ColumnSortOrder::new('otherfield', ColumnSortOrder::DIRECTION_DESC);
        $toSet = SortOrders::new($sort1, $sort2);
        $expected = SortOrders::new();

        $this->assertSame($opts, $opts->setSortOrders($toSet));
        $this->assertSame($opts, $opts->resetSortOrders());

        $this->assertEquals($expected, $opts->getSortOrders());
    }

    public function testPaginationCanBeSet(): void
    {
        $opts = new ListOptions();

        $p = new Pagination(1, 20);

        $this->assertSame($opts, $opts->setPagination($p));

        $this->assertEquals($p, $opts->getPagination());
    }


    public function testPaginationCanBeReset(): void
    {
        $opts = new ListOptions();

        $p = new Pagination(1, 20);

        $this->assertSame($opts, $opts->setPagination($p));
        $this->assertSame($opts, $opts->resetPagination());

        $this->assertNull($opts->getPagination());
    }
}
