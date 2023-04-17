<?php

namespace App\Tests\Unit\Common\Handler;

use App\Common\Handler\ResponseStatus;
use App\Tests\Unit\TestCase;

class ResponseStatusTest extends TestCase
{
    public function testNewOkCanBeCreated(): void
    {
        $status = ResponseStatus::newOK();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_OK, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_OK, $status->getName());
    }

    public function testNewErrorCanBeCreated(): void
    {
        $status = ResponseStatus::newError();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_ERROR, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_ERROR, $status->getName());
    }

    public function testNewCreatedCanBeCreated(): void
    {
        $status = ResponseStatus::newCreated();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_CREATED, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_CREATED, $status->getName());
    }

    public function testNewValidationErrorCanBeCreated(): void
    {
        $status = ResponseStatus::newValidationError();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_VALIDATION_ERROR, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_VALIDATION_ERROR, $status->getName());
    }

    public function testIsValidStatus(): void
    {
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_CREATED));
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_OK));
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_VALIDATION_ERROR));
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_ERROR));

        /* Anything that isn't one of the STATUS_* constants should not be valid */
        $this->assertFalse(ResponseStatus::isValidStatus('not-a-valid-status'));
    }
}
