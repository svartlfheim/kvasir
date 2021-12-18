<?php

namespace App\Tests\Unit\Common\Handler;

use App\Tests\Unit\TestCase;
use App\Common\Handler\ResponseStatus;

class ResponseStatusTest extends TestCase
{
    public function testNewOkCanBeCreated()
    {
        $status = ResponseStatus::newOK();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_OK, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_OK, $status->getName());
    }

    public function testNewErrorCanBeCreated()
    {
        $status = ResponseStatus::newError();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_ERROR, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_ERROR, $status->getName());
    }

    public function testNewCreatedCanBeCreated()
    {
        $status = ResponseStatus::newCreated();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_CREATED, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_CREATED, $status->getName());
    }

    public function testNewValidationErrorCanBeCreated()
    {
        $status = ResponseStatus::newValidationError();
        $this->assertInstanceOf(ResponseStatus::class, $status);
        $this->assertEquals(ResponseStatus::STATUS_VALIDATION_ERROR, (string) $status);
        $this->assertEquals(ResponseStatus::STATUS_VALIDATION_ERROR, $status->getName());
    }

    public function testIsValidStatus()
    {
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_CREATED));
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_OK));
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_VALIDATION_ERROR));
        $this->assertTrue(ResponseStatus::isValidStatus(ResponseStatus::STATUS_ERROR));

        /* Anything that isn't one of the STATUS_* constants should not be valid */
        $this->assertFalse(ResponseStatus::isValidStatus('not-a-valid-status'));
    }
}
