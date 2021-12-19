<?php

namespace App\Tests\Unit\Common\API;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\JSONSerializableInterface;
use App\Common\Handler\ResponseStatus;
use App\Tests\Unit\TestCase;
use ReflectionClass;

class HTTPResponseBuilderTest extends TestCase
{
    public function buildJSONSerializableObject(array $data = []): JSONSerializableInterface
    {
        return new class ($data) implements JSONSerializableInterface {
            protected array $return;

            public function __construct(array $return)
            {
                $this->return = $return;
            }

            public function toJSON(): array
            {
                return $this->return;
            }
        };
    }
    public function testTheSupportedResponseStatuses(): void
    {
        $testObj = new HTTPResponseBuilder();

        $errors = $this->createMock(FieldValidationErrorList::class);
        $errors->expects($this->exactly(4))->method('toJSON')->willReturn([]);

        $this->assertEquals(
            200,
            $testObj->json(
                $this->buildJSONSerializableObject(),
                $this->buildJSONSerializableObject(),
                $errors,
                ResponseStatus::newOK()
            )->getStatusCode()
        );
        $this->assertEquals(
            201,
            $testObj->json(
                $this->buildJSONSerializableObject(),
                $this->buildJSONSerializableObject(),
                $errors,
                ResponseStatus::newCreated()
            )->getStatusCode()
        );
        $this->assertEquals(
            422,
            $testObj->json(
                $this->buildJSONSerializableObject(),
                $this->buildJSONSerializableObject(),
                $errors,
                ResponseStatus::newValidationError()
            )->getStatusCode()
        );
        $this->assertEquals(
            500,
            $testObj->json(
                $this->buildJSONSerializableObject(),
                $this->buildJSONSerializableObject(),
                $errors,
                ResponseStatus::newError()
            )->getStatusCode()
        );
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

        $errors = $this->createMock(FieldValidationErrorList::class);
        $errors->expects($this->exactly(1))->method('toJSON')->willReturn([]);

        $testObj = new HTTPResponseBuilder();

        /*
         We can't assert the exception class, as it's been reflected.
         The actual exception will a ReflectionException...
         */
        $this->expectExceptionMessage("Could not map status 'fake_status' to http code.");
        $testObj->json(
            $this->buildJSONSerializableObject(),
            $this->buildJSONSerializableObject(),
            $errors,
            $resp
        );
    }

    public function testThatTheJSONResponseIsBuiltCorrectly(): void
    {
        $testObj = new HTTPResponseBuilder();

        $meta = $this->buildJSONSerializableObject([
            'metakey' => 'metavalue',
        ]);

        $data = $this->buildJSONSerializableObject([
            'datakey' => 'datavalue',
        ]);

        $errors = $this->createMock(FieldValidationErrorList::class);
        $errors->expects($this->once())->method('toJSON')->willReturn([
            'field' => [
                'rule' => 'myrule',
                'message' => 'mymessage',
            ],
        ]);

        $resp = $testObj->json($meta, $data, $errors, ResponseStatus::newOK());

        $this->assertEquals(json_encode([
            'meta' => [
                'metakey' => 'metavalue',
            ],
            'data' => [
                'datakey' => 'datavalue',
            ],
            'errors' => [
                'field' => [
                    'rule' => 'myrule',
                    'message' => 'mymessage',
                ],
            ],
        ]), $resp->getContent());
        $this->assertEquals(200, $resp->getStatusCode());
    }

    public function testThatDataCanBeNull(): void
    {
        $testObj = new HTTPResponseBuilder();

        $meta = $this->buildJSONSerializableObject();
        $errors = $this->createMock(FieldValidationErrorList::class);
        $errors->expects($this->once())->method('toJSON')->willReturn([]);
        $resp = $testObj->json($meta, null, $errors, ResponseStatus::newOK());

        $this->assertEquals(json_encode([
            'meta' => [],
            'data' => null,
            'errors' => [],
        ]), $resp->getContent());
        $this->assertEquals(200, $resp->getStatusCode());
    }
}
