<?php

namespace MaartenGDev;


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
        $words = $this->parser->parse($data);


        var_dump($words);
    }

    public function searchAttraction($description)
    {

        return $this->client->request('GET', 'http://localhost:9200/trips/attraction/_search', [
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
        ])->getBody()->read(20000);
    }

    public function searchShop($type, $name)
    {
        return $this->client->request('GET', 'http://localhost:9200/trips/shop/_search', [
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
                            ],
                            [
                                "match" => [
                                    "dutch_long_description" => $type
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ])->getBody()->read(20000);
    }

    public function searchParking($longitude, $latitude)
    {
        return $this->client->request('GET', 'http://localhost:9200/trips/attraction/_search', [
            'json' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'dutch_long_description' => '',
                            ]
                        ],
                        'must_not' => [
                            'match' => [
                                'dutch_long_description' => ''
                            ]
                        ]
                    ]
                ]
            ]
        ])->getBody()->read(200000);
    }
}