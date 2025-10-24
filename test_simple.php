<?php

// Test the simple POST endpoint
$url = 'http://localhost:8000/api/v1/psgc/test';
$data = json_encode(['test' => 'data']);

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => $data
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Response: " . $result . "\n";

