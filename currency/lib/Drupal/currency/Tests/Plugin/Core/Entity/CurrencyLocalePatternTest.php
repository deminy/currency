<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\Core\Entity\CurrencyLocalePatternTest.
 */

namespace Drupal\currency\Tests\Plugin\Core\Entity;

use Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern;
use Drupal\simpletest\WebTestBase;

/**
 * Tests class Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern.
 */
class CurrencyLocalePatternTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern',
      'group' => 'Currency',
    );
  }

  /**
   * Test format().
   */
  function testFormat() {
    $currency = entity_load('currency', 'EUR');
    $locale_pattern = entity_create('currency_locale_pattern', array(
      'pattern' => '¤-#,##0.00[XXX][999]',
      'decimalSeparator' => ',',
      'groupingSeparator' => '.',
    ));
    $amount = 12345.6789;
    $formatted = $locale_pattern->format($currency, $amount);
    $formatted_expected = '€-12.345,6789EUR978';
    $this->assertEqual($formatted, $formatted_expected);
  }
}
