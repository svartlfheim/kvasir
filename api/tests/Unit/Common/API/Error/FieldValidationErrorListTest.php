<?php

namespace App\Tests\Unit\Common\API\Error;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\Error\Violation;
use App\Common\API\JSONSerializableInterface;
use App\Tests\Unit\TestCase;
use Countable;
use Iterator;

class FieldValidationErrorListTest extends TestCase
{
    public function testImplementsCorrectInterfaces(): void
    {
        $implemented = class_implements(FieldValidationErrorList::class);

        $this->assertContains(Countable::class, $implemented);
        $this->assertContains(Iterator::class, $implemented);
        $this->assertContains(JSONSerializableInterface::class, $implemented);
    }

    public function testEmptyConstruct(): void
    {
        $list = FieldValidationErrorList::empty();

        $this->assertEquals(0, count($list));
    }

    public function testErrorsCanBeAdded(): void
    {
        $error = $this->createMock(FieldValidationError::class);
        $list = FieldValidationErrorList::empty();

        $this->assertEquals(0, count($list));
        $list->add($error);
        $this->assertEquals(1, count($list));

        $this->assertSame($error, $list->current());
    }

    public function testItIsIterable(): void
    {
        $error = $this->createMock(FieldValidationError::class);
        $error2 = $this->createMock(FieldValidationError::class);
        $errors = [
            $error,
            $error2,
        ];
        $list = FieldValidationErrorList::empty();

        $this->assertEquals(0, count($list));
        $list->add($error);
        $list->add($error2);
        $this->assertEquals(2, count($list));

        foreach ($list as $i => $e) {
            $this->assertSame($errors[$i], $e);
        }
    }

    public function testSerialization(): void
    {
        $error1Violation1 = $this->createMock(Violation::class);
        $error1Violation1->expects($this->once())->method('getRule')->willReturn('error1-rule1');
        $error1Violation1->expects($this->once())->method('getMessage')->willReturn('error1-message1');

        $error1Violation2 = $this->createMock(Violation::class);
        $error1Violation2->expects($this->once())->method('getRule')->willReturn('error1-rule2');
        $error1Violation2->expects($this->once())->method('getMessage')->willReturn('error1-message2');

        $error1Violations = [$error1Violation1, $error1Violation2];

        $error = $this->createMock(FieldValidationError::class);
        $error->expects($this->once())->method('getFieldName')->willReturn('field1');
        $error->expects($this->once())->method('getViolations')->willReturn($error1Violations);

        $error2Violation1 = $this->createMock(Violation::class);
        $error2Violation1->expects($this->once())->method('getRule')->willReturn('error2-rule1');
        $error2Violation1->expects($this->once())->method('getMessage')->willReturn('error2-message1');

        $error2Violation2 = $this->createMock(Violation::class);
        $error2Violation2->expects($this->once())->method('getRule')->willReturn('error2-rule2');
        $error2Violation2->expects($this->once())->method('getMessage')->willReturn('error2-message2');

        $error2Violations = [$error2Violation1, $error2Violation2];

        $error2 = $this->createMock(FieldValidationError::class);
        $error2->expects($this->once())->method('getFieldName')->willReturn('field2');
        $error2->expects($this->once())->method('getViolations')->willReturn($error2Violations);
        $list = FieldValidationErrorList::empty();

        $list->add($error);
        $list->add($error2);

        $this->assertEquals([
            'field1' => [
                [
                    'rule' => 'error1-rule1',
                    'message' => 'error1-message1',
                ],
                [
                    'rule' => 'error1-rule2',
                    'message' => 'error1-message2',
                ],
            ],
            'field2' => [
                [
                    'rule' => 'error2-rule1',
                    'message' => 'error2-message1',
                ],
                [
                    'rule' => 'error2-rule2',
                    'message' => 'error2-message2',
                ],
            ]
        ], $list->toJSON());
    }
}
