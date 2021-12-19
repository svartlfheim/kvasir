<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\Error\Violation;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\ListConnections;
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
        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, []);

        $cmd = $this->createMock(ListConnectionsInterface::class);
        $cmd->expects($this->once())->method('getOrderField')->willReturn('somefield');
        $cmd->expects($this->once())->method('getOrderDirection')->willReturn('somedir');

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new ListConnections();
        $handler->withValidator($validator);


        $expectPagination = new PaginationData();
        $expectPagination->withOrderBy('somefield', 'somedir');

        $this->assertEquals(
            new ListConnectionsResponse(
                $cmd,
                ResponseStatus::newOK(),
                FieldValidationErrorList::empty(),
                ConnectionList::empty(),
                $expectPagination,
            ),
            $handler($cmd)
        );
    }

    public function testValidationErrorHandling(): void
    {
        $mockConstraintViolation = $this->createMock(ConstraintViolation::class);
        $mockConstraintViolation->expects($this->once())->method('getPropertyPath')->willReturn('somefield');
        $mockConstraintViolation->expects($this->once())->method('getMessage')->willReturn('some error');
        $mockConstraintViolation->expects($this->once())->method('getConstraint')->willReturn(new Type('string'));

        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$mockConstraintViolation]);

        $cmd = $this->createMock(ListConnectionsInterface::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new ListConnections();
        $handler->withValidator($validator);

        $expectErrors = FieldValidationErrorList::empty();
        $expectErrors->add(FieldValidationError::new('somefield', [new Violation('some error', 'type')]));

        $this->assertEquals(
            new ListConnectionsResponse(
                $cmd,
                ResponseStatus::newValidationError(),
                $expectErrors,
                ConnectionList::empty(),
                new PaginationData(),
            ),
            $handler($cmd)
        );
    }

    // public function testItIsInvokable(): void
    // {
    //     $cmd = $this->createMock(ListConnectionsInterface::class);
    //     $handler = new ListConnections();

    //     $resp = $handler($cmd);
    //     $this->assertInstanceOf(ListConnectionsResponse::class, $resp);
    //     $this->assertEquals(ResponseStatus::newOK(), $resp->getStatus());
    //     $this->assertEquals(ConnectionList::empty(), $resp->getConnections());
    //     $this->assertSame($cmd, $resp->getCommand());
    //     $this->assertEquals(
    //         (new PaginationData())->withOrderBy('name', 'ASC'),
    //         $resp->getPagination(),
    //     );
    // }
}
