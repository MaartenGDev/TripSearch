<?php
use App\Transformers\AttractionTransformer;
use App\Transformers\ParkingTransformer;
use App\Transformers\ShopTransformer;
use App\Transformers\SearchTransformer;

use App\Client;
use App\Parsers\DescriptionParser;
use App\Engines\SearchEngine;
use App\Parsers\SentenceParser;

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

$searchTransformer = new SearchTransformer($attractionTransformer,$shopTransformer,$parkingTransformer);

$sentence = isset($_POST['search']) && strlen($_POST['search']) > 5 ? htmlspecialchars($_POST['search']) : 'Met de auto naar een museum over kunst en daarna chinees eten.';

$searchTransformer->setSearchQuery($sentence);

$result = $client->search($sentence);

$searchResult = $searchTransformer->transform($result);


header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

echo json_encode($searchResult);