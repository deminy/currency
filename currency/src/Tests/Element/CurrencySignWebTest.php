<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Element\CurrencySignWebTest.
 */

namespace Drupal\currency\Tests\Element;

use Drupal\currency\Element\CurrencySign;
use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency_sign form element.
 */
class CurrencySignWebTest extends WebTestBase {

  public static $modules = array('currency_test');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Element\CurrencySign web test',
      'group' => 'Currency',
    );
  }

  /**
   * Test validation.
   */
  function testValidation() {
    $state = \Drupal::state();
    $path = 'currency_test-form-element-currency-sign';

    // Test an empty sign.
    $values =  array(
      'container[sign][sign]' => '',
    );
    $this->drupalPostForm($path, $values, t('Submit'));
    $sign = $state->get('currency_test_currency_sign_element');
    $this->assertEqual('', $sign);

    // Test a suggested sign.
    $values =  array(
      'container[sign][sign]' => '€',
    );
    $this->drupalPostForm($path . '/EUR', $values, t('Submit'));
    $sign = $state->get('currency_test_currency_sign_element');
    $this->assertEqual('€', $sign);

    // Test a custom sign.
    $values =  array(
      'container[sign][sign]' => CurrencySign::CUSTOM_VALUE,
      'container[sign][sign_custom]' => 'foobar',
    );
    $this->drupalPostForm($path, $values, t('Submit'));
    $sign = $state->get('currency_test_currency_sign_element');
    $this->assertEqual('foobar', $sign);
    $this->drupalGet($path . '/EUR/foobar');
    $this->assertRaw('<option value="' . CurrencySign::CUSTOM_VALUE . '" selected="selected">');
    // Check if the sign element is set to a custom value.
    $this->assertFieldByXPath("//select[@name='container[sign][sign]']/option[@value='" . CurrencySign::CUSTOM_VALUE . "' and @selected='selected']");
    // Check if the custom sign input element has the custom sign as its value.
    $this->assertFieldByXPath("//input[@name='container[sign][sign_custom]' and @value='foobar']");

    // Test a non-existing currency.
    $values =  array(
      'container[sign][sign]' => '',
    );
    $this->drupalPostForm($path . '/ZZZ', $values, t('Submit'));
    $sign = $state->get('currency_test_currency_sign_element');
    $this->assertEqual('', $sign);
  }
}
