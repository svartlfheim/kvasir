<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\Error\Violation;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\CreateConnection;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Tests\Unit\TestCase;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateConnectionTest extends TestCase
{
    public function testSuccessfulHandling(): void
    {
        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, []);

        $cmd = $this->createMock(CreateConnectionInterface::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($constraintViolationList);

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
        $mockConstraintViolation->expects($this->exactly(1))->method('getPropertyPath')->willReturn('somefield');
        $mockConstraintViolation->expects($this->exactly(1))->method('getMessage')->willReturn('some error');
        $mockConstraintViolation->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $constraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$mockConstraintViolation]);

        $cmd = $this->createMock(CreateConnectionInterface::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($constraintViolationList);

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
