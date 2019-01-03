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
    public function setFormat(string $format = Format::PLAIN_TEXT) : WikipediaInfoBoxParser
    {
        $this->format = $format;
        return $this;
    }

    private function buildUrl()
    {
        return $this->endpoint.$this->queryString.$this->article;
    }

    public function parse() : array
    {
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

        return $result;
    }

    private function parseLine(string $line) : ?\stdClass
    {
        $line = trim($line);
        $line = str_replace('| ', '', $line);
        $line = trim($line);
        $parts = explode('=', $line, 2);

        if (count($parts)!==2) {
            return null;
        }

        $result = new \stdClass();
        $result->key = trim($parts[0]);
        $result->value = $this->parseValue($parts[1]);

        return $result;
    }

    private function parseValue(string $value) : string
    {
        $value = trim($value);
        $value = strip_tags($value);

        preg_match_all('/\[\[.*?\]\]/', $value, $matches);

        $matches = $matches[0];

        foreach($matches as $match) {
            $replace = $match;
            $replace = str_replace(['[[', ']]'], '', $replace);
            $pipePos = strpos($replace, '|');
            if ($pipePos !== false) {
                $replace = substr($replace, 0, $pipePos);
            }

            $value = str_replace($match, $replace, $value);
        }

        return $value;
    }
}