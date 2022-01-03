<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\PaginationData;
use App\Common\Command\CommandValidatorInterface;
use App\Common\Database\ColumnSortOrder;
use App\Common\Database\ListOptions;
use App\Common\Database\Pagination;
use App\Common\Database\SortOrders;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\ListConnections;
use App\Connections\Handler\Response\Factory;
use App\Connections\Handler\Response\ListConnectionsResponse;
use App\Connections\Model\ConnectionList;
use App\Connections\Repository\ConnectionsInterface;
use App\Tests\Unit\TestCase;

class ListConnectionsTest extends TestCase
{
    public function testSuccessfulHandling(): void
    {
        $cmd = $this->createMock(ListConnectionsInterface::class);
        $cmd->expects($this->exactly(1))->method('getOrderField')->willReturn('myfield');
        $cmd->expects($this->exactly(1))->method('getOrderDirection')->willReturn(ColumnSortOrder::DIRECTION_ASC);
        $cmd->expects($this->exactly(1))->method('getPage')->willReturn(1);
        $cmd->expects($this->exactly(1))->method('getPageSize')->willReturn(20);

        $mockConnList = $this->createMock(ConnectionList::class);

        $mockResponse = $this->createMock(ListConnectionsResponse::class);
        $mockResponse->expects($this->exactly(1))->method('setCommand')->with($cmd)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setStatus')->with(ResponseStatus::newOK())->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setConnections')->with($mockConnList)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);
        $expectedPaginationData = (new PaginationData())
            ->withOrderBy('myfield', ColumnSortOrder::DIRECTION_ASC)
            ->withPage(1)
            ->withPageSize(20);
        $mockResponse->expects($this->exactly(1))->method('setPagination')->with($expectedPaginationData)->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(ListConnectionsResponse::class)->willReturn($mockResponse);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(1))->method('isEmpty')->willReturn(true);

        $validator = $this->createMock(CommandValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($mockErrors);

        $mockRepo = $this->createMock(ConnectionsInterface::class);
        $expectedListOptions = new ListOptions();
        $expectedListOptions->setPagination(new Pagination(1, 20))
            ->setSortOrders(SortOrders::new(ColumnSortOrder::new('myfield', ColumnSortOrder::DIRECTION_ASC)));
        $mockRepo->expects($this->exactly(1))->method('all')->with($expectedListOptions)->willReturn($mockConnList);

        $handler = new ListConnections($mockFactory, $validator, $mockRepo);

        $this->assertSame(
            $mockResponse,
            $handler($cmd)
        );
    }

    public function testValidationErrorHandling(): void
    {
        $cmd = $this->createMock(ListConnectionsInterface::class);

        $mockResponse = $this->createMock(ListConnectionsResponse::class);
        $mockResponse->expects($this->exactly(1))->method('setCommand')->with($cmd)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setStatus')->with(ResponseStatus::newValidationError())->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setConnections')->with(ConnectionList::empty())->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setPagination')->with(new PaginationData())->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(ListConnectionsResponse::class)->willReturn($mockResponse);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(1))->method('isEmpty')->willReturn(false);

        $validator = $this->createMock(CommandValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($mockErrors);

        $mockRepo = $this->createMock(ConnectionsInterface::class);

        $handler = new ListConnections($mockFactory, $validator, $mockRepo);

        $this->assertSame(
            $mockResponse,
            $handler($cmd)
        );
    }
}
