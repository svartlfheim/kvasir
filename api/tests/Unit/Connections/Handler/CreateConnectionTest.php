<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Tests\Unit\TestCase;
use App\Common\API\Error\Violation;
use App\Common\Handler\ResponseStatus;
use App\Connections\Handler\CreateConnection;
use App\Common\API\Error\FieldValidationError;
use Symfony\Component\Validator\Constraints\Type;
use App\Common\API\Error\FieldValidationErrorList;
use Symfony\Component\Validator\ConstraintViolation;
use App\Connections\Command\CreateConnectionInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Connections\Handler\Response\CreateConnectionResponse;

class CreateConnectionTest extends TestCase
{
    public function testSuccessfulHandling(): void
    {
        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, []);

        $cmd = $this->createMock(CreateConnectionInterface::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new CreateConnection();
        $handler->withValidator($validator);

        $this->assertEquals(
            new CreateConnectionResponse(
                $cmd,
                ResponseStatus::newCreated(),
                FieldValidationErrorList::empty(),
                null,
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

        $cmd = $this->createMock(CreateConnectionInterface::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('validate')->with($cmd)->willReturn($constraintViolationList);

        $handler = new CreateConnection();
        $handler->withValidator($validator);

        $expectErrors = FieldValidationErrorList::empty();
        $expectErrors->add(FieldValidationError::new('somefield', [new Violation('some error', 'type')]));

        $this->assertEquals(
            new CreateConnectionResponse(
                $cmd,
                ResponseStatus::newValidationError(),
                $expectErrors,
                null,
            ),
            $handler($cmd)
        );
    }
}
