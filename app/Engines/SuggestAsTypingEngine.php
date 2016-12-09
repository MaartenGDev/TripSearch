<?php

namespace App\Engines;


use App\Parsers\Parser;

class SuggestAsTypingEngine implements Engine
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function parse($data)
    {
        $result = [];
        $sentence = implode(' ', $this->parser->parse($data));

        if ($this->containsParking($data)) {
            $parkingStringLocation = $this->getPositionOfString('parking', 'auto', $data);
            $sentence = $this->removeTransportation($sentence);

            $result['parking'] = $parkingStringLocation;
        }

        if ($this->containsAttraction($data)) {
            $attraction = $this->getAttractions($sentence);

            $attractionStringLocation = $this->getPositionOfString('attraction', trim($attraction), $data);

            $sentence = $this->removeAttraction($sentence);

            $result['attraction'] = $attractionStringLocation;

        }

        if ($this->containsShop($data)) {
            $shop = $this->getShop($sentence);
            $shopStringLocation = $this->getPositionOfString('shop', trim($shop), $data);
            $result['shop'] = $shopStringLocation;
        }

        return $result;
    }

    protected function containsParking($sentence)
    {
        return strpos($sentence, 'auto') !== false;
    }

    protected function containsShop($sentence)
    {
        return strpos($sentence, '  en ') !== false && count(explode(' en ', $sentence)) > 0;
    }

    protected function containsAttraction($sentence)
    {
        return strpos($sentence, ' en ') !== false && count(explode(' en ', $sentence)) > 1;
    }

    protected function getPositionOfString($type, $part, $sentence)
    {

        $sentenceParts = explode(' ', $part);

        if (count($sentenceParts) > 1) {
            $startOfPart = strpos($sentence, $sentenceParts[0]);
            $lastWord = $sentenceParts[array_reverse(array_keys($sentenceParts))[0]];
            $endOfPart = strpos($sentence, $lastWord) + strlen($lastWord);
        } else {
            $startOfPart = strpos($sentence, $part);
            $endOfPart = $startOfPart + strlen($part);
        }


        return (object)['type' => $type, 'start' => $startOfPart, 'end' => $endOfPart];
    }

    protected function removeTransportation($sentence)
    {
        return str_replace(['auto', 'fiets'], '', $sentence);
    }

    protected function removeAttraction($sentence)
    {
        $words = explode(' en ', $sentence);
        return $words[1];
    }

    protected function getShop($sentence)
    {
        return $sentence;
    }

    protected function getTransportation($sentence)
    {
        if (strpos($sentence, 'auto') !== false) {
            return 'car';
        }

        if (strpos($sentence, 'fiets') !== false) {
            return 'bicycle';
        }

        return 'car';
    }

    protected function getAttractions($sentence)
    {
        $words = explode(' en ', $sentence);

        return $words[0];
    }
}