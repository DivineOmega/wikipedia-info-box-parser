<?php

use DivineOmega\WikipediaInfoBoxParser\Enums\Format;
use DivineOmega\WikipediaInfoBoxParser\WikipediaInfoBoxParser;

require_once __DIR__.'/../vendor/autoload.php';

$infobox = (new WikipediaInfoBoxParser())
    ->setArticle('PHP')
    ->setFormat(Format::PLAIN_TEXT)
//  ->setFormat(Format::HTML)
    ->parse();

var_dump($infobox);