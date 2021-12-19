<?php

namespace App\Tests\Unit\Common\Handler;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\Error\Violation;
use App\Common\Handler\ValidatesCommand;
use App\Connections\Command\ListConnectionsInterface;
use App\Tests\Unit\TestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatesCommandTest extends TestCase
{
    public function buildTestObject(ValidatorInterface $validator): object
    {
        return new class ($validator) {
            use ValidatesCommand;

            protected ValidatorInterface $validator;

            public function __construct(ValidatorInterface $validator)
            {
                $this->validator = $validator;
            }

            protected function getValidator(): ValidatorInterface
            {
                return $this->validator;
            }

            public function doValidateCommand($cmd): FieldValidationErrorList
            {
                return $this->validateCommand($cmd);
            }

            public function doViolationsByProperty(ConstraintViolationList $errors): array
            {
                return $this->violationsByProperty($errors);
            }

            public function doBaseName($value): string
            {
                return $this->baseName($value);
            }
        };
    }

    public function testWhenNoValidationErrorsOccur(): void
    {
        $list = $this->buildMockIteratorAggregate(ConstraintViolationList::class, []);

        $v = $this->createMock(ValidatorInterface::class);
        $v->expects($this->exactly(1))->method('validate')->willReturn($list);
        $testObj = $this->buildTestObject($v);
        // Juts picked one at random, it is irrelevant what object we pass
        $cmd = $this->createMock(ListConnectionsInterface::class);

        $errors = $testObj->doValidateCommand($cmd);

        $this->assertEquals(FieldValidationErrorList::empty(), $errors);
    }

    public function testSingleErrorForFields(): void
    {
        $error1 = $this->createMock(ConstraintViolation::class);
        $error1->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field1');
        $error1->expects($this->exactly(1))->method('getMessage')->willReturn('error1message');
        $error1->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $error2 = $this->createMock(ConstraintViolation::class);
        $error2->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field2');
        $error2->expects($this->exactly(1))->method('getMessage')->willReturn('error2message');
        $error2->expects($this->exactly(1))->method('getConstraint')->willReturn(new Length(null, 0, 10));

        $list = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$error1, $error2]);

        $v = $this->createMock(ValidatorInterface::class);
        $v->expects($this->exactly(1))->method('validate')->willReturn($list);
        $testObj = $this->buildTestObject($v);
        // Juts picked one at random, it is irrelevant what object we pass
        $cmd = $this->createMock(ListConnectionsInterface::class);

        $errors = $testObj->doValidateCommand($cmd);

        $expect = FieldValidationErrorList::empty();
        $expect->add(FieldValidationError::new('field1', [new Violation('error1message', 'type')]));
        $expect->add(FieldValidationError::new('field2', [new Violation('error2message', 'length')]));

        $this->assertEquals($expect, $errors);
    }

    public function testMultipleErrorsForFields(): void
    {
        $error1 = $this->createMock(ConstraintViolation::class);
        $error1->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field1');
        $error1->expects($this->exactly(1))->method('getMessage')->willReturn('error1message');
        $error1->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $error2 = $this->createMock(ConstraintViolation::class);
        $error2->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field1');
        $error2->expects($this->exactly(1))->method('getMessage')->willReturn('error2message');
        $error2->expects($this->exactly(1))->method('getConstraint')->willReturn(new Length(null, 0, 10));

        $error3 = $this->createMock(ConstraintViolation::class);
        $error3->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field2');
        $error3->expects($this->exactly(1))->method('getMessage')->willReturn('error3message');
        $error3->expects($this->exactly(1))->method('getConstraint')->willReturn(new Length(null, 0, 10));

        $list = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$error1, $error2, $error3]);

        $v = $this->createMock(ValidatorInterface::class);
        $v->expects($this->exactly(1))->method('validate')->willReturn($list);
        $testObj = $this->buildTestObject($v);
        // Juts picked one at random, it is irrelevant what object we pass
        $cmd = $this->createMock(ListConnectionsInterface::class);

        $errors = $testObj->doValidateCommand($cmd);

        $expect = FieldValidationErrorList::empty();
        $expect->add(FieldValidationError::new(
            'field1',
            [
                new Violation('error1message', 'type'),
                new Violation('error2message', 'length'),
            ]
        ));
        $expect->add(FieldValidationError::new(
            'field2',
            [
                new Violation('error3message', 'length')
            ]
        ));

        $this->assertEquals($expect, $errors);
    }

    public function testGroupingViolationsByProperty(): void
    {
        $error1 = $this->createMock(ConstraintViolation::class);
        $error1->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field1');
        $error1->expects($this->exactly(1))->method('getMessage')->willReturn('error1message');
        $error1->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $error2 = $this->createMock(ConstraintViolation::class);
        $error2->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field1');
        $error2->expects($this->exactly(1))->method('getMessage')->willReturn('error1message2');
        $error2->expects($this->exactly(1))->method('getConstraint')->willReturn(new Length(null, 0, 10));

        $error3 = $this->createMock(ConstraintViolation::class);
        $error3->expects($this->exactly(1))->method('getPropertyPath')->willReturn('field2');
        $error3->expects($this->exactly(1))->method('getMessage')->willReturn('error3message');
        $error3->expects($this->exactly(1))->method('getConstraint')->willReturn(new Length(null, 0, 10));

        $list = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$error1, $error2, $error3]);

        $testObj = $this->buildTestObject($this->createMock(ValidatorInterface::class));

        $this->assertEquals([
            'field1' => [
                new Violation('error1message', 'type'),
                new Violation('error1message2', 'length'),
            ],
            'field2' => [
                new Violation('error3message', 'length'),
            ]
        ], $testObj->doViolationsByProperty($list));
    }

    public function testGeneratingBaseName(): void
    {
        $testObj = $this->buildTestObject($this->createMock(ValidatorInterface::class));

        $this->assertEquals(
            'type',
            $testObj->doBaseName(new Type('string'))
        );

        $this->assertEquals(
            'string',
            $testObj->doBaseName('somestring')
        );
    }
}
