<?php

namespace App\Tests\Unit\Common\Database;

use App\Common\Database\Pagination;
use App\Tests\Unit\TestCase;

class PaginationTest extends TestCase
{
    public function testGetters(): void
    {
        $p = new Pagination(1, 20);

        $this->assertEquals(1, $p->getPage());
        $this->assertEquals(20, $p->getPageSize());
    }

    public function testCalculatesOffset(): void
    {
        $p = new Pagination(1, 20);
        $this->assertEquals(0, $p->calculateOffset());

        $p = new Pagination(2, 20);
        $this->assertEquals(20, $p->calculateOffset());

        $p = new Pagination(3, 90);
        $this->assertEquals(180, $p->calculateOffset());
    }
}
