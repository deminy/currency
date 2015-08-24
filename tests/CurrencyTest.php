<?php

/**
 * @file
 * Contains class \BartFeenstra\Tests\Currency\CurrencyTest.
 */

namespace BartFeenstra\Tests\Currency;

use BartFeenstra\Currency\Currency;
use BartFeenstra\Currency\Usage;

/**
 * @coversDefaultClass \BartFeenstra\Currency\Currency
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers ::resourceListAll
   * @covers ::resourceDir
   */
  function testResourceList() {
    $list = Currency::resourceListAll();
    foreach ($list as $iso_4217_code) {
      $this->assertSame(strlen($iso_4217_code), 3, 'Currency::getList() returns an array with three-letter strings (ISO 4217 codes).');
    }
  }

  /**
   * Returns JSON for a Currency object.
   *
   * @return string
   */
  function json() {
    return <<<'EOD'
{
    "alternativeSigns": [],
    "ISO4217Code": "EUR",
    "ISO4217Number": "978",
    "sign": "€",
    "subunits": 100,
    "title": "euro",
    "usage": [
        {
            "ISO8601From": "2003-02-04",
            "ISO8601To": "2006-06-03",
            "ISO3166Code": "CS"
        }
    ]
}

EOD;
  }

  /**
   * Returns a Currency object.
   *
   * @return Currency
   */
  function currency() {
    $usage = new Usage();
    $usage->ISO8601From = '2003-02-04';
    $usage->ISO8601To = '2006-06-03';
    $usage->ISO3166Code = 'CS';
    $currency = new Currency();
    $currency->ISO4217Code = 'EUR';
    $currency->ISO4217Number = '978';
    $currency->sign = '€';
    $currency->subunits = 100;
    $currency->title = 'euro';
    $currency->usage = [$usage];

    return $currency;
  }

  /**
   * @covers ::resourceParse
   */
  function testResourceParse() {
    $json = $this->json();
    $currency_parsed = new Currency();
    $currency_parsed->resourceParse($json);
    $this->assertInstanceOf('BartFeenstra\Currency\Currency', $currency_parsed);
    $this->assertInstanceOf('BartFeenstra\Currency\Usage', $currency_parsed->usage[0], 'Currency::parse() parses YAML code to a Usage object.');
    $currency = $this->currency();
    $this->assertSame(get_object_vars($currency->usage[0]), get_object_vars($currency_parsed->usage[0]), 'Currency::parse() parses YAML code to an identical Usage object.');
    unset($currency->usage);
    unset($currency_parsed->usage);
    $this->assertSame(get_object_vars($currency), get_object_vars($currency_parsed), 'Currency::parse() parses YAML code to an identical currency object.');
  }

  /**
   * @covers ::resourceDump
   */
  function testResourceDump() {
    $currency = $this->currency();
    $json = $this->json();
    $json_dumped = $currency->resourceDump();
    $this->assertSame(trim($json), trim($json_dumped));
  }

  /**
   * @covers ::resourceLoad
   * @covers ::resourceDir
   */
  function testResourceLoad() {
    $currency = new Currency();
    $currency->resourceLoad('EUR');
    $this->assertInstanceOf('BartFeenstra\Currency\Currency', $currency, 'Currency::load() loads a single currency from file.');
    $error = FALSE;
    try {
      $currency->resourceLoad('123');
    }
    catch (\RuntimeException $e) {
      $error = TRUE;
    }
    $this->assertTrue($error);
  }

  /**
   * @covers ::getDecimals
   */
  function testGetDecimals() {
    $currencies = [
      'MGA' => 1,
      'EUR' => 2,
      'JPY' => 3,
    ];
    foreach ($currencies as $currency_code => $decimals) {
      $currency = new Currency();
      $currency->resourceLoad($currency_code);
      $this->assertSame($currency->getDecimals(), $decimals);
    }
  }
}
