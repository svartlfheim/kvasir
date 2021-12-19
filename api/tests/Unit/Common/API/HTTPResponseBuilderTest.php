<?php

namespace App\Tests\Unit\Common\API;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\Error\Violation;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\JSONSerializableInterface;
use App\Common\Attributes\HTTPField;
use App\Common\Handler\ResponseStatus;
use App\Tests\Unit\TestCase;
use ReflectionClass;
use RuntimeException;

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

    public function testStatusGuard(): void
    {
        $testObj = new HTTPResponseBuilder();

        $this->expectExceptionObject(new RuntimeException("Response status cannot be null in an HTTP response."));
        $testObj->json();
    }

    public function testTheSupportedResponseStatuses(): void
    {
        $testObj = new HTTPResponseBuilder();

        $this->assertEquals(
            200,
            $testObj->withStatus(ResponseStatus::newOK())->json()->getStatusCode()
        );
        $this->assertEquals(
            201,
            $testObj->withStatus(ResponseStatus::newCreated())->json()->getStatusCode()
        );
        $this->assertEquals(
            422,
            $testObj->withStatus(ResponseStatus::newValidationError())->json()->getStatusCode()
        );
        $this->assertEquals(
            500,
            $testObj->withStatus(ResponseStatus::newError())->json()->getStatusCode()
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

        $testObj = new HTTPResponseBuilder();

        /*
         We can't assert the exception class, as it's been reflected.
         The actual exception will a ReflectionException...
         */
        $this->expectExceptionMessage("Could not map status 'fake_status' to http code.");
        $testObj->withStatus($resp)->json();
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

        $resp = $testObj->withMeta($meta)
            ->withData($data)
            ->withErrors($errors)
            ->withStatus(ResponseStatus::newOK())
            ->json();

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

        $resp = $testObj->withMeta($meta)
        ->withErrors($errors)
        ->withStatus(ResponseStatus::newOK())
        ->json();

        $this->assertEquals(json_encode([
            'meta' => [],
            'data' => null,
            'errors' => [],
        ]), $resp->getContent());
        $this->assertEquals(200, $resp->getStatusCode());
    }

    public function testErrorHTTPMapping(): void
    {
        $mockCommand = new class () {
            #[HTTPField('my_prop')]
            protected string $myProp;
        };


        $mockViolation = $this->createMock(Violation::class);
        $mockViolation->expects($this->once())->method('getRule')->willReturn('some_rule');
        $mockViolation->expects($this->once())->method('getMessage')->willReturn('some_message');
        $mockViolations = [$mockViolation];

        $error = $this->createMock(FieldValidationError::class);
        $error->expects($this->once())->method('getFieldName')->willReturn('myProp');
        $error->expects($this->once())->method('getViolations')->willReturn($mockViolations);

        $errors = $this->buildMockIterator(FieldValidationErrorList::class, [$error]);
        $testObj = new HTTPResponseBuilder();

        $resp = $testObj->withHTTPMappedErrors($errors, $mockCommand)
            ->withStatus(ResponseStatus::newOK())
            ->json();

        $this->assertEquals(json_encode([
            'meta' => [],
            'data' => null,
            'errors'=> [
                // Without mapping this would be myProp
                'my_prop' => [
                    [
                        'rule' => 'some_rule',
                        'message' => 'some_message',
                    ],
                ],
            ],
        ]), $resp->getContent());
    }
}
