<?php

namespace DivineOmega\WikipediaInfoBoxParser;


use DivineOmega\WikipediaInfoBoxParser\Enums\Format;
use Psr\Cache\CacheItemPoolInterface;

class WikitextParser
{
    private $wikitext;
    private $format = Format::PLAIN_TEXT;

    private $endpoint = 'https://en.wikipedia.org/w/api.php';
    private $queryString = '?action=parse&format=json&contentmodel=wikitext&text=';

    public function setCache(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cache = $cacheItemPool;
        return $this;
    }

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
        $cacheKey = sha1(serialize(['wikitext', $this->wikitext, $this->format]));

        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return $item->get();
        }

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

        $item->set($returnValue);
        $this->cache->save($item);

        return $returnValue;
    }
}