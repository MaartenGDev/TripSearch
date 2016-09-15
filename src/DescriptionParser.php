<?php

namespace MaartenGDev;


class DescriptionParser implements Parser
{
    protected $verbs = [];
    protected $commonWords = ['de','het','een','te','er','dit','uit','tot','deze','aan', 'ook',
        'in','bij','van','waar','is','met','en', 'of', 'alsof', 'maar', 'doch', 'noch', 'dus', 'derhalve',
        'daarom', 'doordat', 'door', 'terwijl', 'omdat', 'aangezien', 'want', 'daar', 'dewijl',
        'doordien', 'naardien', 'nademaal', 'overmits', 'vermits', 'wijl', 'indien', 'ingeval',
        'zo', 'zodat', 'opdat', 'sinds', 'sedert', 'nadat', 'vooraleer', 'voor', 'aleer', 'eer',
        'voordat', 'totdat', 'toen', 'zodra', 'als', 'zoals', 'alhoewel', 'hoewel', 'ofschoon',
        'schoon', 'mits', 'tenware', 'tenzij', 'naar', 'naargelang', 'naarmate,wanneer'
    ];
    protected $missingVerbs = ['ligt','heeft','ook','die','dan','hier','nog'];

    protected $punctuation = ['.','?','!',':',';',',','-'];
    protected $nouns = ['ik','mij','jij','je','jou','u','hij','hem','zij','ze','haar','hem','het','wij','we','ons','jullie','hun','hen','ze'];

    protected $data;

    public function __construct($source)
    {
        $this->verbs = json_decode(file_get_contents($source));
    }

    protected function splitIntoWords()
    {
        $this->data = explode(' ', $this->data);

        return $this;
    }

    protected function removeTags()
    {
        $this->data = strip_tags($this->data);

        return $this;
    }

    protected function removeVerbs()
    {
        $this->data = array_filter($this->data, function ($word) {
            return !in_array(ucfirst($word), $this->verbs);
        });

        return $this;
    }

    protected function removeCommonWords()
    {
        $this->data = array_filter($this->data, function ($word) {
            return !in_array(strtolower($word), $this->commonWords);
        });


        return $this;
    }

    protected function removePunctuation()
    {
        $this->data = array_filter($this->data, function ($word) {
            return !in_array(strtolower($word), $this->punctuation);
        });


        return $this;
    }

    protected function removeNouns(){
        $this->data = array_filter($this->data, function ($word) {
            return !in_array(strtolower($word), $this->nouns);
        });


        return $this;
    }

    protected function removeMissedVerbs(){
        $this->data = array_filter($this->data, function ($word) {
            return !in_array(strtolower($word), $this->missingVerbs);
        });


        return $this;
    }

    protected function get()
    {
        return $this->data;
    }

    public function parse($data)
    {
        $this->data = $data;

        return $this->removeTags()
            ->splitIntoWords()
            ->removeVerbs()
            ->removeCommonWords()
            ->removePunctuation()
            ->removeNouns()
            ->removeMissedVerbs()
            ->get();
    }
}