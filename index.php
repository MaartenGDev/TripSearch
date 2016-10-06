<?php
use MaartenGDev\AttractionTransformer;
use MaartenGDev\Client;
use MaartenGDev\DescriptionParser;
use MaartenGDev\ParkingTransformer;
use MaartenGDev\SearchEngine;
use MaartenGDev\SentenceParser;
use MaartenGDev\ShopTransformer;

require_once 'vendor/autoload.php';

$guzzle = new GuzzleHttp\Client();
$source = $_SERVER['DOCUMENT_ROOT'] . '/cache/verbs.json';

$parser = new DescriptionParser($source);
$sentenceParser = new SentenceParser($source);

$engine = new SearchEngine($guzzle, $sentenceParser);

$client = new Client($guzzle,$parser,$engine);

$attractionTransformer = new AttractionTransformer();
$shopTransformer = new ShopTransformer();
$parkingTransformer = new ParkingTransformer();
$sentence = isset($_POST['search']) ? $_POST['search'] : 'Met de auto naar een museum over kunst en daarna een ijsje eten.';

$result = $client->search($sentence);

$searchResult = [];

$searchResult['attraction'] = $attractionTransformer->transform($result['attraction']);
$searchResult['shop'] = $shopTransformer->transform($result['shop']);
$searchResult['parking'] = $parkingTransformer->transform($result['parking']);

echo json_encode($searchResult);