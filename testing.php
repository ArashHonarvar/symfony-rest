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
    'nickname' => $nickName,
    'avatarNumber' => 2,
    'tagLine' => 'A good Programmer'
];

// Create Programmer
$response = $client->post("/api/programmers", [
    'body' => json_encode($data)
]);

// Show One Programmer
$programmerUrl = $response->getHeader('location')[0];
$response = $client->get($programmerUrl);

// Show Collection of Programmers
$response = $client->get("/api/programmers");


echo "Status Code: ";
echo $response->getStatusCode();
echo "\n";
foreach ($response->getHeaders() as $key => $header) {
    echo $key . ": ";
    echo $header[0];
    echo "\n";
}
echo "Body: ";
echo $response->getBody();
echo "\n";
