<?php

require __DIR__ . "/vendor/autoload.php";

$client = new \GuzzleHttp\Client([
    "base_uri" => "http://127.0.0.1:8000",
    "defaults" => [
        "exceptions" => false
    ]
]);

$nickName = "Programmer" . rand(0, 99999);
$data = [
    'nickName' => $nickName,
    'avatarNumber' => 2,
    'tagLine' => 'A good Programmer'
];

$response = $client->post("/api/programmers" , [
    'body' => json_encode($data)
]);

echo $response->getStatusCode();
echo "\n\n";
echo $response->getHeader('content-type')[0];
echo "\n\n";
echo $response->getBody();
echo "\n\n";
