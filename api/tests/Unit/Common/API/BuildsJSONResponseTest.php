<?php

namespace App\Tests\Unit\Common\API;

use ReflectionClass;
use App\Tests\Unit\TestCase;
use App\Common\Handler\ResponseStatus;
use App\Common\API\JSONSerializableInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Tests\Unit\Common\API\Stubs\BuildsJSONResponseTestTarget;

class BuildsJSONResponseTest extends TestCase
{
    public function testTheSupportedResponseStatuses(): void
    {
        $testObj = new BuildsJSONResponseTestTarget();

        $this->assertEquals(200, $testObj->getHTTPStatusCode(ResponseStatus::newOK()));
        $this->assertEquals(422, $testObj->getHTTPStatusCode(ResponseStatus::newValidationError()));
        $this->assertEquals(500, $testObj->getHTTPStatusCode(ResponseStatus::newError()));
        $this->assertEquals(201, $testObj->getHTTPStatusCode(ResponseStatus::newCreated()));
    }

    /**
     * Feels pretty nasty, but required to keep the constructor protected.
     * The constructor being protected makes testing harder, but should make the code more robust.
     * It ensures that the instance is always made correctly, so the 'name' cannot be something invalid.
     */
    public function testAnUnSupportedResponseStatus(): void
    {
        $reflectedResp = new ReflectionClass(ResponseStatus::class);
        $constructor = $reflectedResp->getConstructor();
        $constructor->setAccessible(true);
        $reflectionProperty = $reflectedResp->getProperty('name');
        $reflectionProperty->setAccessible(true);

        $resp = $reflectedResp->newInstanceWithoutConstructor();
        $constructor->invoke($resp, 'fake_status');

        $testObj = new BuildsJSONResponseTestTarget();

        /*
         We can't assert the exception class, as it's been reflected.
         The actual exception will a ReflectionException...
         */
        $this->expectExceptionMessage("Could not map status 'fake_status' to http code.");
        $this->assertEquals(200, $testObj->getHTTPStatusCode($resp));
    }

    public function testThatTheJSONResponseIsBuiltCorrectly(): void
    {
        $testObj = new BuildsJSONResponseTestTarget();

        $metaClass = new class () implements JSONSerializableInterface {
            public function toJSON(): array
            {
                return [
                    'metakey' => 'metavalue',
                ];
            }
        };

        $dataClass = new class () implements JSONSerializableInterface {
            public function toJSON(): array
            {
                return [
                    'datakey' => 'datavalue',
                ];
            }
        };

        $resp = $testObj->buildResponse(new $metaClass(), new $dataClass(), ResponseStatus::newOK());

        $this->assertEquals(new JsonResponse([
            'meta' => [
                'metakey' => 'metavalue',
            ],
            'data' => [
                'datakey' => 'datavalue',
            ],
        ], 200), $resp);
    }
}
