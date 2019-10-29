<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;


use App\Controller\Api\ProgrammerController;

use App\Tests\ApiTestCase;
use PHPUnit\Framework\TestCase;


class ProgrammerControllerTest extends ApiTestCase
{

    public function testPOST()
    {

        $nickName = "Programmer" . rand(0, 99999);
        $data = [
            'nickname' => $nickName,
            'avatarNumber' => 2,
            'tagLine' => 'A good Programmer'
        ];

        // Create Programmer
        $response = $this->client->post("/api/programmers", [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('location'));
        $finishedData = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('nickname', $finishedData);
    }


}
