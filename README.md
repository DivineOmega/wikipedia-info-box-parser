# Wikipedia Info Box Parser

This library parses the info boxes on Wikipedia pages 
into an associative array.

## Installation

The Wikipedia Info Box Parser package can be easily
installed with Composer. Simply run the following
command from the root of your project.

```bash
composer require divineomega/wikipedia-info-box-parser
```

## Usage

See the example usage below.

```php
use DivineOmega\WikipediaInfoBoxParser\Enums\Format;
use DivineOmega\WikipediaInfoBoxParser\WikipediaInfoBoxParser;

require_once __DIR__.'/../vendor/autoload.php';

$infobox = (new WikipediaInfoBoxParser())
    ->setArticle('GitHub')
    ->setFormat(Format::PLAIN_TEXT)
//  ->setFormat(Format::HTML)
    ->parse();

var_dump($infobox);
```

```php
array(21) {
  ["name"]=>
  string(12) "GitHub, Inc."
  ["logo"]=>
  string(27) "GitHub logo 2013 padded.svg"
  ["company_type"]=>
  string(10) "Subsidiary"
  ["founded"]=>
  string(70) "February 8, 2008; 10 years ago (2008-02-08) (as Logical Awesome LLC)"
  ["location"]=>
  string(31) "San Francisco, California, U.S."
  ["area served"]=>
  string(9) "Worldwide"
  ["founder"]=>
  string(60) "Tom Preston-Werner  Chris Wanstrath  P. J. HyettScott Chacon"
  ["CEO"]=>
  string(12) "Nat Friedman"
  ["key people"]=>
  string(17) "P. J. Hyett (COO)"
  ["industry"]=>
  string(8) "Software"
  ["international"]=>
  string(3) "Yes"
  ["employees"]=>
  string(6) "800[1]"
  ["url"]=>
  string(10) "github.com"
  ["programming language"]=>
  string(4) "Ruby"
  ["website type"]=>
  string(30) "Git-repository hosting service"
  ["registration"]=>
  string(53) "Optional (required for creating and joining projects)"
  ["users"]=>
  string(25) "31 million (October 2018)"
  ["language"]=>
  string(7) "English"
  ["launched"]=>
  string(43) "April 10, 2008; 10 years ago (2008-04-10)"
  ["current status"]=>
  string(6) "Active"
  ["parent"]=>
  string(24) "Microsoft (2018-present)"
}

```