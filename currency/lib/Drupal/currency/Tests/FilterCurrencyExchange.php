<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\FilterCurrencyExchange.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency_exchange input filter.
 */
class FilterCurrencyExchange extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'name' => 'currency_exchange input filter',
      'group' => 'Currency',
    );
  }

  /**
   * Test the currency_exchange input filter.
   */
  function testCurrencyExchange() {
    $tokens_valid = array(
      '[currency:EUR:NLG]' => '2.20371',
      '[currency:EUR:NLG:1]' => '2.20371',
      '[currency:EUR:NLG:2]' => '4.40742',
    );
    $tokens_invalid = array(
      // Missing arguments.
      '[currency]',
      '[currency:]',
      '[currency::]',
      '[currency:EUR]',
      // Invalid currency code.
      '[currency:EUR:123]',
      '[currency:123:EUR]',
      // Invalid currency code and missing argument.
      '[currency:123]',
    );
    $format = entity_create('filter_format', array(
      'format' => 'currency_exchange',
      'name' => 'Currency format',
      'filters' => array(
        'currency_exchange' => array(
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