<?php
use MaartenGDev\Client;
use MaartenGDev\DescriptionParser;
use MaartenGDev\SearchEngine;
use MaartenGDev\SentenceParser;

require_once 'vendor/autoload.php';

$guzzle = new GuzzleHttp\Client();
$source = $_SERVER['DOCUMENT_ROOT'] . '/cache/verbs.json';

$parser = new DescriptionParser($source);
$sentenceParser = new SentenceParser($source);

$engine = new SearchEngine($guzzle, $sentenceParser);


$client = new Client($guzzle,$parser,$engine);

echo $client->search('Met de auto naar een museum over kunst en daarna een ijsje eten.');

