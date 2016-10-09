<?php

namespace App\Transformers;


class SearchTransformer implements Transformer
{

    /**
     * @var AttractionTransformer
     */
    protected $attraction;
    /**
     * @var ShopTransformer
     */
    protected $shop;
    /**
     * @var ParkingTransformer
     */
    protected $parking;

    protected $searchQuery;

    public function __construct(AttractionTransformer $attraction, ShopTransformer $shop, ParkingTransformer $parking)
    {
        $this->attraction = $attraction;
        $this->shop = $shop;
        $this->parking = $parking;
    }

    public function setSearchQuery($query){
        $this->searchQuery = $query;
    }

    public function getSearchQuery(){
        return $this->searchQuery;
    }
    public function transform($data)
    {

        $result = [];
        $result['search'] = $this->getSearchQuery();
        $result['result_title'] = 'Cultureel Uitje';

        $result['data']['attraction'] = $this->attraction->transform($data['attraction']);
        $result['data']['shop'] = $this->shop->transform($data['shop']);
        $result['data']['parking'] = $this->parking->transform($data['parking']);

        return $result;
    }
}