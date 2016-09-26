<?php
use MaartenGDev\Client;
use MaartenGDev\DescriptionParser;
require_once 'vendor/autoload.php';

$guzzle = new GuzzleHttp\Client();
$source = $_SERVER['DOCUMENT_ROOT'] . '/cache/verbs.json';

$parser = new DescriptionParser($source);

$client = new Client($guzzle,$parser);

var_dump($client->setupSearch());

