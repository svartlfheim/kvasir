<?php

namespace App\Tests\Unit\Common\API\Error;

use App\Common\API\Error\Violation;
use App\Tests\Unit\TestCase;

class ViolationTest extends TestCase
{
    public function testGetters(): void
    {
        $v = new Violation('message', 'rule');
        $this->assertEquals('message', $v->getMessage());
        $this->assertEquals('rule', $v->getRule());
    }
}
