<?php

namespace App\Tests\Unit\Common\DI;

use ReflectionObject;
use App\Tests\Unit\TestCase;
use App\Common\DI\RequiresValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequiresValidatorTest extends TestCase
{
    public function testItSetsValidatorInternally(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $testClass = new class () {
            use RequiresValidator;
        };

        $test = new $testClass();

        $test->withValidator($validator);

        $reflection = new ReflectionObject($test);
        $prop = $reflection->getProperty('validator');
        $prop->setAccessible(true);

        $this->assertSame($validator, $prop->getValue($test));
    }
}
