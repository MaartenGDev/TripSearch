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
        try {
            $this->client->request('PUT', $this->url, [
                    'json' => $data
                ]
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    protected function deleteIndex()
    {
        $this->setUrl('http://localhost:9200');

        try {
            $this->client->request('DELETE', $this->url . '/trips');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function setupStructure()
    {

        $data = [
            'mappings' => (object)[
                'attraction' => (object)[
                    'properties' => [
                        'title' => (object)[
                            'type' => 'string',
                        ],
                        'dutch_title' => (object)[
                            'type' => 'string',
                            'analyzer' => 'dutch'
                        ],
                        'short_description' => (object)[
                            'type' => 'string',
                        ],
                        'dutch_short_description' => (object)[
                            'type' => 'string',
                            'analyzer' => 'dutch'
                        ],
                        'long_description' => (object)[
                            'type' => 'string',
                        ],
                        'dutch_long_description' => (object)[
                            'type' => 'string',
                            'analyzer' => 'dutch'
                        ],
                        'description_words' => [
                            'type' => 'string'
                        ],
                        'media' => [
                            'properties' => [
                                'main' => [
                                    'type' => 'string'
                                ],
                                'url' => [
                                    'type' => 'string',
                                ]
                            ]
                        ],
                        'location' => [
                            'properties' => [
                                'name' => [
                                    'type' => 'string'
                                ],
                                'city' => [
                                    'type' => 'string',
                                ],
                                'address' => [
                                    'type' => 'string',
                                ],
                                'zipcode' => [
                                    'type' => 'string',
                                ],
                                'latitude' => [
                                    'type' => 'double',
                                ],
                                'longitude' => [
                                    'type' => 'double'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->setUrl('http://localhost:9200/trips');
        $this->put($data);
    }

    public function setupSearch()
    {
        $this->deleteIndex();
        $this->setupStructure();

        $attractions = file_get_contents('cache/Attracties.json');
        $index = 1;

        foreach (json_decode($attractions) as $item) {

            $shortDescription = strip_tags($item->details->nl->shortdescription);
            $longDescription = strip_tags($item->details->nl->shortdescription);


            $data = [
                'title' => $item->title,
                'dutch_title' => $item->title,
                'short_description' => $shortDescription,
                'dutch_short_description' => $shortDescription,
                'long_description' => $longDescription,
                'dutch_long_description' => $longDescription,
                'description_words' => array_values($this->parser->parse($longDescription)),
                'media' => $item->media,
                'location' => [
                    'name' => $item->location->name,
                    'city' => $item->location->city,
                    'address' => $item->location->adress,
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

    public function search($search)
    {
        return $this->client->request('GET', 'http://localhost:9200/trips/attraction/_search?explain', [
            'json' => [
                'query' => [
                    'dis_max' => [
                        'queries' => [
                            [
                                "match" => [
                                    "dutch_title" => $search
                                ]
                            ],
                            [
                                "match" => [
                                    "dutch_long_description" => $search
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ])->getBody()->read(20000);
    }
}