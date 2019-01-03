<?php

namespace DivineOmega\WikipediaInfoBoxParser;

use DivineOmega\WikipediaInfoBoxParser\Enums\Format;

class WikipediaInfoBoxParser
{
    private $article;
    private $format;

    private $endpoint = 'https://en.wikipedia.org/w/api.php';
    private $queryString = '?format=json&action=query&prop=revisions&rvprop=content&rvslots=main&titles=';

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
    public function setFormat(string $format) : WikipediaInfoBoxParser
    {
        $this->format = $format;
        return $this;
    }

    private function buildUrl()
    {
        return $this->endpoint.$this->queryString.$this->article;
    }

    public function parse()
    {
        $url = $this->buildUrl();

        $data = json_decode(file_get_contents($url), true);
        $pages = $data['query']['pages'];
        $page = reset($pages);
        $content = $page['revisions'][0]['slots']['main']['*'];

        preg_match_all('/{{Infobox(.*?)\R}}/sm', $content, $matches);

        var_dump($matches[1][0]);

        return null;
    }
}