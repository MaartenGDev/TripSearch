<?php
namespace MaartenGDev;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Middleware;

class Client implements ClientInterface
{
    protected $client;
    protected $url;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function put($data)
    {
        $this->client->request('PUT', $this->url, [
                'json' => $data
            ]
        );
    }

    public function storeDate()
    {
        $attractions = file_get_contents('cache/Attracties.json');
        $index = 1;

        foreach (json_decode($attractions) as $item) {

            $data = [
                'title' => $item->title,
                'short_description' => $item->details->nl->shortdescription,
                'long_description' => $item->details->nl->longdescription,
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
                'query' => [
                    'match_phrase' => [
                        'long_description' => 'object maakt onderdeel'
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        'long_description' => (object) []
                    ]
                ]
            ]
        ])->getBody()->read(20000);
    }
}