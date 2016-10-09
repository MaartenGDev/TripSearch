<?php
namespace App;

use App\Engines\SearchEngine;
use GuzzleHttp\Client as GuzzleClient;
use App\Parsers\Parser;
use GuzzleHttp\Exception\ClientException;

class Client implements ClientInterface
{
    protected $client;
    protected $url;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var Engine|SearchEngine
     */
    private $engine;

    /**
     * Client constructor.
     * @param GuzzleClient $client
     * @param Parser $parser
     * @param Engine $engine
     */
    public function __construct(GuzzleClient $client, Parser $parser, SearchEngine $engine)
    {
        $this->client = $client;
        $this->parser = $parser;
        $this->engine = $engine;
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
        } catch (ClientException $e) {
            echo $e->getResponse()->getBody()->getContents();
            var_dump(debug_backtrace());
            die();
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
                    'location_details' => [
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
                            ]
                        ]
                    ],
                    'location' => [
                        'type' => 'geo_point'
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
                    'location_details' => [
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
                            ]
                        ]
                    ],
                    'location' => [
                        'type' => 'geo_point'
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
                    'location_details' => [
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
                            ]
                        ]
                    ],
                    'location' => [
                        'type' => 'geo_point'
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
                'location_details' => (object) [
                    'name' => $item->location->name,
                    'city' => $item->location->city,
                    'address' => $item->location->adress,
                    'zipcode' => $item->location->zipcode,
                ],
                'location' => (object) [
                    'lat' => $this->convertCoordinate($item->location->latitude),
                    'lon' => $this->convertCoordinate($item->location->longitude)
                ]
            ];

            $this->setUrl('http://localhost:9200/trips/attraction/' . $index);
            $this->put($data);
            $index++;


        }

    }

    public function getParkData()
    {
        $json = file_get_contents('cache/park.json');
        $data = json_decode($json)->parkeerlocaties;

        return array_map(function ($parking) {
            $parking = $parking->parkeerlocatie;

            $location = json_decode($parking->Locatie);

            return (object)[
                'title' => $parking->title,
                'dutch_title' => $parking->title,
                'type' => $parking->type,
                'url' => $parking->url,
                'description' => $parking->opmerkingen,
                'dutch_description' => $parking->opmerkingen,
                'location_details' => (object) [
                    'name' => $parking->woonplaats,
                    'city' => $parking->woonplaats,
                    'address' => $parking->adres,
                    'zipcode' => $parking->postcode,
                ],
                'location' => (object)[
                    'lat' => $location->coordinates[1],
                    'lon' => $location->coordinates[0],
                ]
            ];
        }, $data);
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
                'location_details' => (object) [
                    'name' => $item->location->name,
                    'city' => $item->location->city,
                    'address' => $item->location->adress,
                    'zipcode' => $item->location->zipcode
                ],
                'location' => (object) [
                    'lat' => $this->convertCoordinate($item->location->latitude),
                    'lon' => $this->convertCoordinate($item->location->longitude)
                ]
            ];

            $this->setUrl('http://localhost:9200/trips/shop/' . $index);
            $this->put($data);
            $index++;

        }
    }

    protected function addParkingItems()
    {

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
                'location_details' => [
                    'name' => $parking->location_details->name,
                    'city' => $parking->location_details->city,
                    'address' => $parking->location_details->address,
                    'zipcode' => $parking->location_details->zipcode,
                ],
                'location' => [
                    'lat' => $this->convertCoordinate($parking->location->lat),
                    'lon' => $this->convertCoordinate($parking->location->lon)
                ]
            ];


            $this->setUrl('http://localhost:9200/trips/parking/' . $index);
            $this->put($data);
            $index++;

        }
    }


    public function search($search)
    {
        $sentence = $this->engine->parse($search);

        return $sentence;
    }
}