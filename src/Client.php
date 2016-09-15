<?php
namespace MaartenGDev;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Middleware;

class Client implements ClientInterface
{
    protected $client;
    protected $url;
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(GuzzleClient $client, Parser $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function put($data)
    {
        try{
            $this->client->request('PUT', $this->url, [
                    'json' => $data
                ]
            );
        }catch(\Exception $e){
            var_dump($e->getMessage());
        }

    }

    public function storeDate()
    {
        $attractions = file_get_contents('cache/Attracties.json');
        $index = 1;

        foreach (json_decode($attractions) as $item) {

            $shortDescription = strip_tags($item->details->nl->shortdescription);
            $longDescription = strip_tags($item->details->nl->shortdescription);


            $data = [
                'title' => $item->title,
                'short_description' => $shortDescription,
                'long_description' => $longDescription,
                'description_words' => array_values($this->parser->parse($longDescription)),
                'location' => [
                    'name' => $item->location->name,
                    'city' => $item->location->city,
                    'adress' => $item->location->adress,
                    'zipcode' => $item->location->zipcode,
                    'latitude' => (float)str_replace(',', '.', $item->location->latitude),
                    'longitude' => (float)str_replace(',', '.', $item->location->longitude)
                ]
            ];

            $this->setUrl('http://localhost:9200/trips/attraction/' . $index);
            $this->put($data);
            $index++;
        }
    }

    public function search()
    {
        return $this->client->request('GET', 'http://localhost:9200/trips/attraction/_search', [
            'json' => [
                "aggs" => [
                    "all_descriptions" => [
                        "terms" => ['field' => 'description_words'],
                    ]
                ]
            ]
        ])->getBody()->read(20000);
    }
}