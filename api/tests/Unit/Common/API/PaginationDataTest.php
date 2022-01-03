<?php

namespace App\Tests\Unit\Common\API;

use App\Common\API\PaginationData;
use App\Tests\Unit\TestCase;

class PaginationDataTest extends TestCase
{
    public function testDefaultStructure(): void
    {
        $pData = new PaginationData();
        $this->assertEquals([
            'page' => null,
            'page_size' => null,
            'order' => [
                'field' => null,
                'direction' => null,
            ],
            'filters' => [],
        ], $pData->toJSON());
    }

    public function testAllPropertiesCanBeAdded(): void
    {
        $pData = new PaginationData();
        $pData->withPage(1)
            ->withPageSize(20)
            ->withFilters(['myfield' => 'somevalue'])
            ->withOrderBy('myfield', 'mydirection');

        $this->assertEquals([
            'page' => 1,
            'page_size' => 20,
            'order' => [
                'field' => 'myfield',
                'direction' => 'mydirection',
            ],
            'filters' => [
                'myfield' => 'somevalue',
            ],
        ], $pData->toJSON());
    }

    public function testAllPropertiesAreOverriddenOnSubsequentCalls(): void
    {
        $pData = new PaginationData();
        $pData->withPage(1)
            ->withPageSize(20)
            ->withFilters(['myfield' => 'somevalue'])
            ->withOrderBy('myfield', 'mydirection');

        $pData->withPage(2)
            ->withPageSize(30)
            ->withFilters(['otherfield' => 'othervalue'])
            ->withOrderBy('otherfield', 'otherdirection');

        $this->assertEquals([
            'page' => 2,
            'page_size' => 30,
            'order' => [
                'field' => 'otherfield',
                'direction' => 'otherdirection',
            ],
            'filters' => [
                'otherfield' => 'othervalue',
            ],
        ], $pData->toJSON());
    }
}
