<?php

namespace DivineOmega\WikipediaInfoBoxParser;


use DivineOmega\WikipediaInfoBoxParser\Enums\Format;

class WikitextParser
{
    private $wikitext;
    private $format = Format::PLAIN_TEXT;

    private $endpoint = 'https://en.wikipedia.org/w/api.php';
    private $queryString = '?action=parse&format=json&contentmodel=wikitext&text=';

    public function setWikitext(string $wikitext) : WikitextParser
    {
        $this->wikitext = $wikitext;
        return $this;
    }

    /**
     * Sets the format you wish the values to parsed into.
     *
     * @see Format
     *
     * @param mixed $format
     * @return WikitextParser
     */
    public function setFormat(string $format = Format::PLAIN_TEXT) : WikitextParser
    {
        $this->format = $format;
        return $this;
    }

    private function buildUrl()
    {
        return $this->endpoint.$this->queryString.urlencode($this->wikitext);
    }

    public function parse()
    {
        $url = $this->buildUrl();

        $data = json_decode(file_get_contents($url), true);

        $dom = new \DOMDocument();
        $dom->loadXML($data['parse']['text']['*']);

        $element = $dom->childNodes[0]->childNodes[0];

        $returnValue = $element->ownerDocument->saveXML($element);

        if ($this->format === Format::PLAIN_TEXT) {
            $returnValue = strip_tags($returnValue);
        }

        $returnValue = trim($returnValue);

        return $returnValue;
    }
}