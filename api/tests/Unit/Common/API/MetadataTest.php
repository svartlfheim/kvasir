<?php

namespace App\Tests\Unit\Common\API;

use App\Common\API\Metadata;
use App\Tests\Unit\TestCase;
use App\Common\API\PaginationData;
use App\Common\API\JSONSerializableInterface;

class MetadataTest extends TestCase
{
    public function testItIsEmptyWithoutAnySpecifiedFields()
    {
        $mData = new Metadata();
        $this->assertEquals([], $mData->toJSON());
    }

    public function testThatPaginationCanBeAdded()
    {
        $pagination = $this->createMock(PaginationData::class);
        $pagination->expects($this->once())
            ->method('toJSON')
            ->willReturn([
                'page' => 1,
                'sort_by' => 'somefield',
            ]);

        $mData = new Metadata();
        $mData->withPagination($pagination);
        $this->assertEquals([
            'pagination' => [
                'page' => 1,
                'sort_by' => 'somefield',
            ]
        ], $mData->toJSON());
    }

    public function testThatGenericFieldsCanBeAdded()
    {
        $value1Class = new class () implements JSONSerializableInterface {
            public function toJSON(): array
            {
                return [
                    'value1key' => 'value1',
                ];
            }
        };
        $value2Class = new class () implements JSONSerializableInterface {
            public function toJSON(): array
            {
                return [
                    'value2key' => 'value2',
                ];
            }
        };

        $mData = new Metadata();
        $mData->withField('key1', new $value1Class());
        $mData->withField('key2', new $value2Class());

        $this->assertEquals([
            'key1' => [
                'value1key' => 'value1',
            ],
            'key2' => [
                'value2key' => 'value2',
            ],
        ], $mData->toJSON());
    }
}
