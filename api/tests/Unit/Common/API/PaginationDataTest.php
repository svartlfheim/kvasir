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
            'next_token' => null,
            'prev_token' => null,
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
        $pData->withNextToken('nextpage')
            ->withPreviousToken('prevpage')
            ->withFilters(['myfield' => 'somevalue'])
            ->withOrderBy('myfield', 'mydirection');

        $this->assertEquals([
            'next_token' => 'nextpage',
            'prev_token' => 'prevpage',
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
        $pData->withNextToken('nextpage')
            ->withPreviousToken('prevpage')
            ->withFilters(['myfield' => 'somevalue'])
            ->withOrderBy('myfield', 'mydirection');

        $pData->withNextToken('othernextpage')
            ->withPreviousToken('otherprevpage')
            ->withFilters(['otherfield' => 'othervalue'])
            ->withOrderBy('otherfield', 'otherdirection');

        $this->assertEquals([
            'next_token' => 'othernextpage',
            'prev_token' => 'otherprevpage',
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
