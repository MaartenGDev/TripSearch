<?php
require_once 'vendor/autoload.php';
$guzzle = new GuzzleHttp\Client();
$source = $_SERVER['DOCUMENT_ROOT'] . '/cache/Attractions.json';
$parser = new \MaartenGDev\DescriptionParser($source);

$client = new \MaartenGDev\Client($guzzle,$parser);


echo $client->searchAndExclude('leuk','huwelijksvoltrekking');

