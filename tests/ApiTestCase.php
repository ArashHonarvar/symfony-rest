<?php
declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use \GuzzleHttp\Client;

class ApiTestCase extends TestCase
{

    private static $staticClient;

    /**
     * @var Client
     */
    protected $client;

    public static function setUpBeforeClass(): void
    {
        self::$staticClient = new Client([
            "base_uri" => "http://127.0.0.1:8000",
            "defaults" => [
                "exceptions" => false
            ]
        ]);
    }

    public function setUp(): void
    {
        $this->client = self::$staticClient;
    }
}
