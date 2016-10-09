<?php

namespace App\Transformers;


class ParkingTransformer implements Transformer
{

    public function transform($data)
    {
        $mainImage = 'https://media.iamsterdam.com/ndtrc/Images/20101028/efd2a27a-8e33-4463-8650-070ff2348f11.jpg';
        $detailsImage ='https://media.iamsterdam.com/ndtrc/Images/20101028/06d3a296-d940-4b9f-bb1d-16f6a0769b7f.jpg';


        return (object)[
            'media' => (object) [
                'main' => $mainImage,
                'details' => $detailsImage
            ],
            'icon' => 'truck',
            'title' => $data->_source->title,
            'short_description' => $data->_source->description,
            'long_description' => $data->_source->description,
            'url' => $data->_source->url,
            'type' => $data->_source->type,
            'location' => (object) [
                'name' => $data->_source->location_details->name,
                'city' => $data->_source->location_details->city,
                'address' => $data->_source->location_details->address,
                'zipcode' => $data->_source->location_details->zipcode,
                'lat' => $data->_source->location->lat,
                'lon' => $data->_source->location->lon
            ]
        ];
    }
}