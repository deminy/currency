<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\Core\Entity\CurrencyTest.
 */

namespace Drupal\currency\Tests\Plugin\Core\Entity;

use Drupal\currency\Plugin\Core\Entity\Currency;
use Drupal\currency\Usage;
use Drupal\simpletest\WebTestBase;

/**
 * Tests class Drupal\currency\Plugin\Core\Entity\Currency.
 */
class CurrencyTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Plugin\Core\Entity\Currency',
      'group' => 'Currency',
    );
  }

  /**
   * Test format().
   */
  function testFormat() {
    $currency = entity_load('currency', 'EUR');
    $amount = 12345.6789;
    $formatted = $currency->format($amount);
    $formatted_expected = '€12,345.6789';
    $this->assertEqual($formatted, $formatted_expected, 'Currency::format() correctly formats an amount.');
  }

  /**
   * Test roundAmount().
   */
  function testRoundAmount() {
    $currency = entity_load('currency', 'JPY');
    $this->assertTrue($currency->roundAmount('12.34'), '12.340');
    $this->assertTrue($currency->roundAmount('1234.5678'), '1234.568');
  }

  /**
   * Tests getDecimals().
   */
  function testGetDecimals() {
    $currencies = array(
      'MGA' => 1,
      'EUR' => 2,
      'JPY' => 3,
    );
    foreach ($currencies as $currency_code => $decimals) {
      $currency = entity_load('currency', $currency_code);
      $this->assertEqual($currency->getDecimals(), $decimals);
    }
  }

  /**
   * Tests isObsolete().
   */
  function testIsObsolete() {
    // A currency without usage data.
    $currency = new Currency(array(), 'currency');
    $this->assertFalse($currency->isObsolete());

    // A currency that is no longer being used.
    $currency->usage[] = new Usage(array(
      'usageFrom' => '1813-01-01',
      'usageTo' => '2002-02-28',
    ));
    $this->assertTrue($currency->isObsolete());

    // A currency that will become obsolete next year.
    $currency->usage[] = new Usage(array(
      'usageFrom' => '1813-01-01',
      'usageTo' => date('o') + 1 . '-02-28',
    ));
    $this->assertFalse($currency->isObsolete());
  }
}
