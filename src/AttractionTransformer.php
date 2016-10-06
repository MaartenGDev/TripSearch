<?php

namespace MaartenGDev;


class AttractionTransformer implements Transformer
{

    public function transform($data)
    {
        return (object)[
            'title' => $data->_source->title,
            'short_description' => $data->_source->short_description,
            'long_description' => $data->_source->long_description,
            'location' => (object) [
                'name' => $data->_source->location_details->name,
                'city' => $data->_source->location_details->city,
                'address' => $data->_source->location_details->address,
                'zipcode' => $data->_source->location_details->zipcode,
                'lat' => $data->_source->location->lat,
                'lon' => $data->_source->location->lon,
            ]
        ];
    }
}