<?php

namespace DivineOmega\WikipediaInfoBoxParser;

use DivineOmega\DOFileCachePSR6\CacheItemPool;
use DivineOmega\WikipediaInfoBoxParser\Enums\Format;
use DivineOmega\WikitextParser\Parser;
use Psr\Cache\CacheItemPoolInterface;

class WikipediaInfoBoxParser
{
    private $article;
    private $format = Format::PLAIN_TEXT;

    private $endpoint = 'https://en.wikipedia.org/w/api.php';
    private $queryString = '?format=json&action=query&prop=revisions&rvprop=content&rvslots=main&titles=';

    /** @var CacheItemPoolInterface */
    private $cache = null;

    public function __construct()
    {
        $cacheItemPool = new CacheItemPool();
        $cacheItemPool->changeConfig([
            'cacheDirectory' => __DIR__.'/../cache/',
        ]);

        $this->setCache($cacheItemPool);
    }

    public function setCache(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cache = $cacheItemPool;
        return $this;
    }

    /**
     * Sets the Wikipedia article you wish to parse the info box for.
     *
     * @param mixed $article
     * @return WikipediaInfoBoxParser
     */
    public function setArticle(string $article) : WikipediaInfoBoxParser
    {
        $this->article = $article;
        return $this;
    }

    /**
     * Sets the format you wish the values to parsed into.
     *
     * @see Format
     *
     * @param mixed $format
     * @return WikipediaInfoBoxParser
     */
    public function setFormat(string $format = Format::PLAIN_TEXT) : WikipediaInfoBoxParser
    {
        $this->format = $format;
        return $this;
    }

    private function buildUrl()
    {
        return $this->endpoint.$this->queryString.urlencode($this->article);
    }

    public function parse() : array
    {
        $cacheKey = sha1(serialize(['infobox', $this->article, $this->format]));

        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return $item->get();
        }

        $url = $this->buildUrl();

        $data = json_decode(file_get_contents($url), true);
        $pages = $data['query']['pages'];
        $page = reset($pages);
        $content = $page['revisions'][0]['slots']['main']['*'];

        preg_match_all('/{{Infobox(.*?)\R}}/sm', $content, $matches);

        $match = $matches[1][0];

        $lines = explode("\n", $match);

        $result = [];

        foreach($lines as $line) {
            $parsedLine = $this->parseLine($line);
            if ($parsedLine) {
                $result[$parsedLine->key] = $parsedLine->value;
            }
        }

        $item->set($result);
        $this->cache->save($item);

        return $result;
    }

    private function parseLine(string $line) : ?\stdClass
    {
        $line = trim($line);
        $parts = explode('=', $line, 2);

        if (count($parts)!==2) {
            return null;
        }

        $result = new \stdClass();
        $result->key = trim(str_replace('|', '', $parts[0]));
        $result->value = $this->parseValue($parts[1]);

        if (!$result->key || !$result->value) {
            return null;
        }

        return $result;
    }

    private function parseValue(string $value) : string
    {
        return (new Parser())
            ->setCache($this->cache)
            ->setWikitext($value)
            ->setFormat($this->format)
            ->parse();
    }
}