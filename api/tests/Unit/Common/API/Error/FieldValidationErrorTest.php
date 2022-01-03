<?php

namespace App\Tests\Unit\Common\API\Error;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\Violation;
use App\Tests\Unit\TestCase;
use DateTime;
use RuntimeException;

class FieldValidationErrorTest extends TestCase
{
    public function testGettersAndSuccessfulConstruction(): void
    {
        $violations = [
            $this->createMock(Violation::class),
            $this->createMock(Violation::class),
        ];
        $e = FieldValidationError::new('myfield', $violations);
        $this->assertEquals('myfield', $e->getFieldName());
        $this->assertEquals($violations, $e->getViolations());
    }

    public function testGuardForViolationsTypes(): void
    {
        $violations = [
            $this->createMock(Violation::class),
            $this->createMock(DateTime::class),
        ];
        $this->expectExceptionObject(new RuntimeException("Message at index '1' must be an instance of " . Violation::class));
        $e = FieldValidationError::new('myfield', $violations);
    }
}
