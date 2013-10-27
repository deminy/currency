<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\FilterCurrencyLocalize.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency_localize input filter.
 */
class FilterCurrencyLocalize extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'currency_localize input filter',
      'group' => 'Currency',
    );
  }

  /**
   * Test the currency_localize input filter.
   */
  function testCurrencyLocalze() {
    $tokens_valid = array(
      '[currency-localize:EUR:100]' => '€100',
      '[currency-localize:EUR:100,3210]' => '€100.3210',
      '[currency-localize:EUR:1.99]' => '€1.99',
      '[currency-localize:EUR:2,99]' => '€2.99',
    );
    $tokens_invalid = array(
      // Missing arguments.
      '[currency-localize]',
      '[currency-localize:]',
      '[currency-localize::]',
      '[currency-localize:EUR]',
      // Invalid currency code.
      '[currency-localize:123:456]',
      // Invalid currency code and missing argument.
      '[currency-localize:123]',
    );
    $format = entity_create('filter_format', array(
      'format' => 'currency_localize',
      'name' => 'Currency format',
      'filters' => array(
        'currency_localize' => array(
          'status' => TRUE,
        ),
      ),
    ));
    $format->save();

    foreach ($tokens_valid as $token => $replacement) {
      $this->assertIdentical(check_markup($token, $format->format), $replacement);
    }
    foreach ($tokens_invalid as $token) {
      $this->assertIdentical(check_markup($token, $format->format), $token);
    }
  }
}