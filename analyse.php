<?php
require_once 'vendor/autoload.php';
$source = $_SERVER['DOCUMENT_ROOT'] . '/cache/verbs.json';

$parser = new \App\Parsers\SentenceParser($source);
$suggestAsTypingEngine = new \App\Engines\SuggestAsTypingEngine($parser);

$sentence = isset($_POST['search']) ? $_POST['search'] : 'Met de auto naar een museum over kunst en daarna een ijsje eten.';

header("Access-Control-Allow-Origin: *");
//header('Content-Type: application/json');

echo json_encode($suggestAsTypingEngine->parse($sentence));