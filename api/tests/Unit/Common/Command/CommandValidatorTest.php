<?php

namespace App\Tests\Unit\Common\Command;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\Error\Violation;
use App\Common\Attributes\HTTPField;
use App\Common\Command\CommandValidator;
use App\Tests\Unit\TestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommandValidatorTest extends TestCase
{
    public function testNoErrors(): void
    {
        $mockCmd = new class () {};

        $mockConstraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, []);

        $mockBaseValidator = $this->createMock(ValidatorInterface::class);
        $mockBaseValidator->expects($this->exactly(1))->method('validate')->with($mockCmd)->willReturn($mockConstraintViolationList);

        $v = new CommandValidator($mockBaseValidator);

        $res = $v->validate($mockCmd);

        $expect = FieldValidationErrorList::empty();

        $this->assertEquals($expect, $res);
    }

    public function testSingleErrorForFields(): void
    {
        $mockCmd = new class () {};

        $mockViolation1 = $this->createMock(ConstraintViolation::class);
        $mockViolation1->expects($this->exactly(1))->method('getPropertyPath')->willReturn('prop1');
        $mockViolation1->expects($this->exactly(1))->method('getMessage')->willReturn('message 1');
        $mockViolation1->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $mockViolation2 = $this->createMock(ConstraintViolation::class);
        $mockViolation2->expects($this->exactly(1))->method('getPropertyPath')->willReturn('prop2');
        $mockViolation2->expects($this->exactly(1))->method('getMessage')->willReturn('message 2');
        $mockViolation2->expects($this->exactly(1))->method('getConstraint')->willReturn(new NotBlank());

        $mockConstraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$mockViolation1, $mockViolation2]);

        $mockBaseValidator = $this->createMock(ValidatorInterface::class);
        $mockBaseValidator->expects($this->exactly(1))->method('validate')->with($mockCmd)->willReturn($mockConstraintViolationList);

        $v = new CommandValidator($mockBaseValidator);

        // $mockCmd = new class() {
        //     #[HTTPField('order_field')]
        //     public $prop1;

        //     public $prop2;
        // };


        $res = $v->validate($mockCmd);

        $expect = FieldValidationErrorList::empty();
        $expect->add(FieldValidationError::new('prop1', [new Violation('message 1', 'type')]));
        $expect->add(FieldValidationError::new('prop2', [new Violation('message 2', 'notblank')]));

        $this->assertEquals($expect, $res);
    }

    public function testMultipleErrorsForFields(): void
    {
        $mockCmd = new class () {};

        $mockViolation1 = $this->createMock(ConstraintViolation::class);
        $mockViolation1->expects($this->exactly(1))->method('getPropertyPath')->willReturn('prop1');
        $mockViolation1->expects($this->exactly(1))->method('getMessage')->willReturn('message 1-1');
        $mockViolation1->expects($this->exactly(1))->method('getConstraint')->willReturn(new Type('string'));

        $mockViolation2 = $this->createMock(ConstraintViolation::class);
        $mockViolation2->expects($this->exactly(1))->method('getPropertyPath')->willReturn('prop2');
        $mockViolation2->expects($this->exactly(1))->method('getMessage')->willReturn('message 2-1');
        $mockViolation2->expects($this->exactly(1))->method('getConstraint')->willReturn(new NotBlank());

        $mockViolation3 = $this->createMock(ConstraintViolation::class);
        $mockViolation3->expects($this->exactly(1))->method('getPropertyPath')->willReturn('prop2');
        $mockViolation3->expects($this->exactly(1))->method('getMessage')->willReturn('message 2-2');
        $mockViolation3->expects($this->exactly(1))->method('getConstraint')->willReturn(new Length(null, 0));

        $mockViolation4 = $this->createMock(ConstraintViolation::class);
        $mockViolation4->expects($this->exactly(1))->method('getPropertyPath')->willReturn('prop1');
        $mockViolation4->expects($this->exactly(1))->method('getMessage')->willReturn('message 1-2');
        $mockViolation4->expects($this->exactly(1))->method('getConstraint')->willReturn(new NotBlank());

        $mockConstraintViolationList = $this->buildMockIteratorAggregate(ConstraintViolationList::class, [$mockViolation1, $mockViolation2, $mockViolation3, $mockViolation4]);

        $mockBaseValidator = $this->createMock(ValidatorInterface::class);
        $mockBaseValidator->expects($this->exactly(1))->method('validate')->with($mockCmd)->willReturn($mockConstraintViolationList);

        $v = new CommandValidator($mockBaseValidator);

        $res = $v->validate($mockCmd);

        $expect = FieldValidationErrorList::empty();
        $expect->add(FieldValidationError::new('prop1', [new Violation('message 1-1', 'type'), new Violation('message 1-2', 'notblank')]));
        $expect->add(FieldValidationError::new('prop2', [new Violation('message 2-1', 'notblank'), new Violation('message 2-2', 'length')]));

        $this->assertEquals($expect, $res);
    }
}
