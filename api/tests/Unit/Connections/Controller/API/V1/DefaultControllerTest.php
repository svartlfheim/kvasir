<?php

namespace App\Tests\Unit\Connections\Controller\API\V1;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Connections\Controller\API\V1\DefaultController;

class DefaultControllerTest extends TestCase
{
    public function testResponse(): void
    {
        $ctrl = new DefaultController();

        $this->assertEquals(
            new JsonResponse(["hello" => "world"]),
            $ctrl->index()
        );
    }
}
