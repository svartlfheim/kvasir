<?php

namespace App\Tests\Unit\Common\API;

use App\Common\API\ArrayData;
use App\Tests\Unit\TestCase;

class ArrayDataTest extends TestCase
{
    /*
        @TODO: write a test that works with objects in the array
        this isn't a high priortiy as we shouldn't rely on that, but it should be handled correctly
    */
    public function testThatTheDataPassedIsReturnedUnchanged(): void
    {
        $testData = [
            'key1' => 'somevalue',
            'key2' => false,
            'key3' => [
                'subkey1' => 'subvalue',
            ],
        ];
        $arrData = new ArrayData($testData);

        $this->assertEquals($testData, $arrData->toJSON());
    }
}
