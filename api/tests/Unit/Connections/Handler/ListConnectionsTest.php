<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\ListConnections;
use App\Connections\Handler\Response\Factory;
use App\Connections\Handler\Response\ListConnectionsResponse;
use App\Connections\Model\ConnectionList;
use App\Tests\Unit\TestCase;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ListConnectionsTest extends TestCase
{
    public function testSuccessfulHandling(): void
    {
        $cmd = $this->createMock(ListConnectionsInterface::class);
        $cmd->expects($this->exactly(1))->method('getOrderField')->willReturn('myfield');
        $cmd->expects($this->exactly(1))->method('getOrderDirection')->willReturn('mydirection');

        $mockResponse = $this->createMock(ListConnectionsResponse::class);
        $mockResponse->expects($this->exactly(1))->method('setCommand')->with($cmd)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setStatus')->with(ResponseStatus::newOK())->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setConnections')->with($this->isInstanceof(ConnectionList::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setPagination')->with((new PaginationData())->withOrderBy('myfield', 'mydirection'))->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(ListConnectionsResponse::class)->willReturn($mockResponse);
        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, []);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new ListConnections($mockFactory);
        $handler->withValidator($validator);

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
        $mockResponse->expects($this->exactly(1))->method('setConnections')->with($this->isInstanceof(ConnectionList::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setPagination')->with(new PaginationData())->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(ListConnectionsResponse::class)->willReturn($mockResponse);

        $mockConstraintViolation = $this->createMock(ConstraintViolation::class);
        $mockConstraintViolation->expects($this->exactly(1))->method('getPropertyPath')->willReturn('somefield');
        $mockConstraintViolation->expects($this->exactly(1))->method('getMessage')->willReturn('some error');
        $mockConstraintViolation->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$mockConstraintViolation]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new ListConnections($mockFactory);
        $handler->withValidator($validator);

        $this->assertSame(
            $mockResponse,
            $handler($cmd)
        );
    }
}
