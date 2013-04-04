<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\CurrencyAmountFormElement.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency_amount form element.
 */
class CurrencyAmountFormElement extends WebTestBase {

  public static $modules = array('currency_test');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'currency_amount form element',
      'group' => 'Currency',
    );
  }

  /**
   * Test validation.
   */
  function testValidation() {
    $path = 'currency_test-form-element-currency-amount/50.00/100';

    // Test valid values.
    $values =  array(
      'amount[amount]' => '50,95',
      'amount[currency_code]' => 'EUR',
    );
    $this->drupalPost($path, $values, t('Submit'));
    $this->assertRaw("\$form_state['amount'] = " . var_export(array(
      'amount' => '50.95',
      'currency_code' => 'EUR',
    ), TRUE));

    // Test valid values with a predefined currency.
    $this->drupalGet($path . '/NLG');
    $this->assertNoFieldByXPath("//input[@name='amount[currency_code]']");
    $values =  array(
      'amount[amount]' => '50,95',
    );
    $this->drupalPost($path . '/NLG', $values, t('Submit'));
    $this->assertRaw("\$form_state['amount'] = " . var_export(array(
      'amount' => '50.95',
      'currency_code' => 'NLG',
    ), TRUE));

    // Test invalid values.
    $invalid_amounts = array(
      // Illegal characters.
      $this->randomName(2),
      // Multiple decimal marks.
      '49,.95',
      // A value that is below the minimum.
      '49.95',
      // A value that exceeds the maximum.
      '999'
    );
    foreach ($invalid_amounts as $amount) {
      $values =  array(
        'amount[amount]' => $amount,
      );
      $this->drupalPost($path, $values, t('Submit'));
      $this->assertFieldByXPath("//input[@name='amount[amount]' and contains(@class, 'error')]");
      $this->assertNoFieldByXPath("//input[not(@name='amount[amount]') and contains(@class, 'error')]");
    }
  }
}
