<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\CreateConnection;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Connections\Handler\Response\Factory;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateConnectionTest extends TestCase
{
    public function testSuccessfulHandling(): void
    {
        $cmd = $this->createMock(CreateConnectionInterface::class);

        $mockResponse = $this->createMock(CreateConnectionResponse::class);
        $mockResponse->expects($this->exactly(1))->method('setCommand')->with($cmd)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setStatus')->with($this->isInstanceOf(ResponseStatus::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setConnection')->with($this->isInstanceof(Connection::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(CreateConnectionResponse::class)->willReturn($mockResponse);
        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, []);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new CreateConnection($mockFactory);
        $handler->withValidator($validator);

        $this->assertSame(
            $mockResponse,
            $handler($cmd)
        );
    }

    public function testValidationErrorHandling(): void
    {
        $cmd = $this->createMock(CreateConnectionInterface::class);

        $mockResponse = $this->createMock(CreateConnectionResponse::class);
        $mockResponse->expects($this->exactly(1))->method('setCommand')->with($cmd)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setStatus')->with(ResponseStatus::newValidationError())->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setConnection')->with(null)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(CreateConnectionResponse::class)->willReturn($mockResponse);

        $mockConstraintViolation = $this->createMock(ConstraintViolation::class);
        $mockConstraintViolation->expects($this->exactly(1))->method('getPropertyPath')->willReturn('somefield');
        $mockConstraintViolation->expects($this->exactly(1))->method('getMessage')->willReturn('some error');
        $mockConstraintViolation->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$mockConstraintViolation]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new CreateConnection($mockFactory);
        $handler->withValidator($validator);

        $this->assertSame(
            $mockResponse,
            $handler($cmd)
        );
    }
}
