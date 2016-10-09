<?php

namespace App\Engines;


use App\Parsers\Parser;
use GuzzleHttp\Client;

class SearchEngine implements Engine
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Client $client, Parser $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    public function parse($data)
    {
        $sentence = implode(' ',$this->parser->parse($data));

        $transportation = $this->getTransportation($sentence);

        $sentence = $this->removeTransportation($sentence);

        $attraction = $this->getAttractions($sentence);

        $sentence = $this->removeAttraction($sentence);

        $shop = $this->getShop($sentence);

        $searchAttraction = $this->searchAttraction($attraction);

        $searchShop = $this->searchShop($shop);


        $shopLatitude = $searchAttraction->_source->location->lat;
        $shopLongitude = $searchAttraction->_source->location->lon;

        $searchTransportation = $this->searchParking($shopLatitude,$shopLongitude);

        return ['shop' => $searchShop, 'attraction' => $searchAttraction, 'parking' => $searchTransportation];
    }

    protected function removeTransportation($sentence){
        return str_replace(['auto','fiets'],'',$sentence);
    }

    protected function removeAttraction($sentence){
        $words = explode(' en ',$sentence);
        return $words[1];
    }

    protected function getShop($sentence){
        return $sentence;
    }

    protected function getTransportation($sentence){
        if(strpos($sentence, 'auto') !== false){
            return 'car';
        }

        if(strpos($sentence, 'fiets') !== false){
            return 'bicycle';
        }

        return 'car';
    }

    protected function getAttractions($sentence){
        $words = explode(' en ',$sentence);

        return $words[0];
    }

    public function searchAttraction($description)
    {

        $data = $this->client->request('GET', 'http://localhost:9200/trips/attraction/_search', [
            'json' => [
                'query' => [
                    'dis_max' => [
                        'queries' => [
                            [
                                "match" => [
                                    "dutch_title" => $description
                                ]
                            ],
                            [
                                "match" => [
                                    "dutch_long_description" => $description
                                ]

                            ]
                        ]
                    ]
                ]
            ]
        ])->getBody()->read(200000);

        return json_decode($data)->hits->hits[0];
    }

    public function searchShop($name)
    {
        $data = $this->client->request('GET', 'http://localhost:9200/trips/shop/_search', [
            'json' => [
                'query' => [
                    'dis_max' => [
                        'queries' => [
                            [
                                "match" => [
                                    "dutch_title" => $name
                                ]
                            ],
                            [
                                "match" => [
                                    "dutch_long_description" => $name
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ])->getBody()->read(200000);

        return json_decode($data)->hits->hits[0];
    }

    public function searchParking($latitude, $longitude)
    {
        $data = $this->client->request('GET', 'http://localhost:9200/trips/parking/_search', [
            'json' => [
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'geo_distance' => [
                                'distance' => '5km',
                                'location' => [
                                    'lat' => $latitude,
                                    'lon' => $longitude
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ])->getBody()->read(200000);

        return json_decode($data)->hits->hits[0];

    }
}