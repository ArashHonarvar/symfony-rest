<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;


use App\Controller\Api\ProgrammerController;

use App\Tests\ApiTestCase;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;


class ProgrammerControllerTest extends ApiTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->createUser("arash");
    }

    public function testPOST()
    {
        $data = [
            'nickname' => 'Programmer',
            'avatarNumber' => 2,
            'tagLine' => 'A good Programmer'
        ];

        // Create Programmer


        try {
            $response = $this->client->post("/api/programmers", [
                'body' => json_encode($data)
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('/api/programmers/Programmer', $response->getHeader('location')[0]);
        $finishedData = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('nickname', $finishedData);
        $this->assertEquals('Programmer', $data['nickname']);
    }

    public function testGetProgrammer()
    {
        $this->createProgrammer([
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ]);

        try {
            $response = $this->client->get('/api/programmers/UnitTester');
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getHeader("Content-type")[0], "application/json");
        $this->asserter()->assertResponsePropertiesExist($response,
            [
                'nickname',
                'avatarNumber',
                'powerLevel',
                'tagLine'
            ]);
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', "UnitTester");
    }

    public function testGETProgrammesCollection()
    {
        $this->createProgrammer([
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ]);
        $this->createProgrammer([
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
        ]);

        try {
            $response = $this->client->get('/api/programmers');
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
            }
        }
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyIsArray($response, "programmers");
        $this->asserter()->assertResponsePropertyCount($response, "programmers", 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'programmers[1].nickname', "CowboyCoder");
    }


    public function testPUTProgrammer()
    {
        $this->createProgrammer([
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
            'tagLine' => 'foo'
        ]);

        $data = [
            'nickname' => 'CowgirlCoder',
            'avatarNumber' => 2,
            'tagLine' => 'foo'
        ];


        try {
            $response = $this->client->put("/api/programmers/CowboyCoder", [
                'body' => json_encode($data)
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'CowboyCoder');
    }


    public function testDELETEProgrammer()
    {
        $this->createProgrammer([
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ]);

        try {
            $response = $this->client->delete('/api/programmers/UnitTester');
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
            }
        }

        $this->assertEquals(204, $response->getStatusCode());
    }


}
