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

    public function patch($data)
    {
        try {
            $this->client->request('patch', $this->url, [
                    'json' => $data
                ]
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    protected function deleteIndex($index)
    {
        $this->setUrl('http://localhost:9200');

        try {
            $this->client->request('DELETE', $this->url . '/' . $index);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function getParkData()
    {
        $json = file_get_contents('cache/park.json');
        $data = json_decode($json)->parkeerlocaties;

        return array_map(function ($parking) {
            $parking = $parking->parkeerlocatie;

            $location = json_decode($parking->Locatie);

            return (object) [
                'title' => $parking->title,
                'dutch_title' => $parking->title,
                'type' => $parking->type,
                'url' => $parking->url,
                'description' => $parking->opmerkingen,
                'dutch_description' => $parking->opmerkingen,
                'location' => (object) [
                    'name' => $parking->woonplaats,
                    'city' => $parking->woonplaats,
                    'address' => $parking->adres,
                    'zipcode' => $parking->postcode,
                    'latitude' => $location->coordinates[1],
                    'longitude' => $location->coordinates[0],
                ]
            ];
        }, $data);
    }

    protected function getParkingStructure()
    {
        return [
            'parking' => (object)[
                'properties' => [
                    'title' => [
                        'type' => 'string',
                    ],
                    'dutch_title' => [
                        'type' => 'string',
                        'analyzer' => 'dutch'
                    ],
                    'type' => [
                        'type' => 'string'
                    ],
                    'url' => [
                        'type' => 'string'
                    ],
                    'description' => [
                        'type' => 'string',
                    ],
                    'dutch_description' => [
                        'type' => 'string',
                        'analyzer' => 'dutch'
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
        ];
    }

    protected function getShopStructure()
    {
        return [
            'shop' => (object)[
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
        ];
    }

    protected function getAttractionStructure()
    {
        return [
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
        ];
    }

    public function setupStructure($mappings)
    {
        $this->setUrl('http://localhost:9200/trips');

        $this->put([
            'mappings' => $mappings
        ]);
    }

    public function setupSearch()
    {
        $this->deleteIndex('trips');

        $this->setupStructure([
            'attraction' => $this->getAttractionStructure(),
            'shop' => $this->getShopStructure(),
            'parking' => $this->getParkingStructure()
        ]);

        $this->addAttractionItems();
        $this->addShopItems();
        $this->addParkingItems();
    }

    protected function addAttractionItems()
    {

        $data = file_get_contents('cache/Attractions.json');
        $index = 1;
        foreach (json_decode($data) as $item) {

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
                    'latitude' => $this->convertCoordinate($item->location->latitude),
                    'longitude' => $this->convertCoordinate($item->location->longitude)
                ]
            ];

            $this->setUrl('http://localhost:9200/trips/attraction/' . $index);
            $this->put($data);
            $index++;

        }
    }
    protected function convertCoordinate($coordinate)
    {
        return (float)str_replace(',', '.', $coordinate);
    }

    protected function addShopItems()
    {

        $data = file_get_contents('cache/Shops.json');
        $index = 1;
        foreach (json_decode($data) as $item) {

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
                    'latitude' => $this->convertCoordinate($item->location->latitude),
                    'longitude' => $this->convertCoordinate($item->location->longitude)
                ]
            ];

            $this->setUrl('http://localhost:9200/trips/shop/' . $index);
            $this->put($data);
            $index++;

        }
    }

    protected function addParkingItems(){

        $items = $this->getParkData();
        $index = 1;
        foreach ($items as $parking) {

            $data = [
                'title' => $parking->title,
                'dutch_title' => $parking->title,
                'type' => $parking->type,
                'url' => $parking->url,
                'description' => $parking->description,
                'dutch_description' => $parking->dutch_description,
                'location' => [
                    'name' => $parking->location->name,
                    'city' => $parking->location->city,
                    'address' => $parking->location->address,
                    'zipcode' => $parking->location->zipcode,
                    'latitude' => $this->convertCoordinate($parking->location->latitude),
                    'longitude' => $this->convertCoordinate($parking->location->longitude)
                ]
            ];



            $this->setUrl('http://localhost:9200/trips/parking/' . $index);
            $this->put($data);
            $index++;

        }
    }

    public function search($search)
    {
        return $this->client->request('GET', 'http://localhost:9200/trips/attraction/_search', [
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

    public function searchAndExclude($search, $wordsToExclude)
    {

        return $this->client->request('GET', 'http://localhost:9200/trips/attraction/_search', [
            'json' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'dutch_long_description' => $search,
                            ]
                        ],
                        'must_not' => [
                            'match' => [
                                'dutch_long_description' => $wordsToExclude
                            ]
                        ]
                    ]
                ]
            ]
        ])->getBody()->read(200000);
    }
}