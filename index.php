<?php
require_once 'vendor/autoload.php';
$guzzle = new GuzzleHttp\Client();
$client = new \MaartenGDev\Client($guzzle);

echo htmlspecialchars($client->search());

