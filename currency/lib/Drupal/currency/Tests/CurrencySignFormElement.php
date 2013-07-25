<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\CurrencySignFormElement.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency_sign form element.
 */
class CurrencySignFormElement extends WebTestBase {

  public static $modules = array('currency_test');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'currency_sign form element',
      'group' => 'Currency',
    );
  }

  /**
   * Test validation.
   */
  function testValidation() {
    $path = 'currency_test-form-element-currency-sign';

    // Test an empty sign.
    $values =  array(
      'sign[sign]' => '',
    );
    $this->drupalPost($path, $values, t('Submit'));
    $this->assertRaw("\$form_state['sign'] = ''");

    // Test a suggested sign.
    $values =  array(
      'sign[sign]' => '€',
    );
    $this->drupalPost($path . '/EUR', $values, t('Submit'));
    $this->assertRaw("\$form_state['sign'] = '€'");

    // Test a custom sign.
    $values =  array(
      'sign[sign]' => CURRENCY_SIGN_FORM_ELEMENT_CUSTOM_VALUE,
      'sign[sign_custom]' => 'foobar',
    );
    $this->drupalPost($path, $values, t('Submit'));
    $this->assertRaw("\$form_state['sign'] = 'foobar'");
    $this->drupalGet($path . '//foobar');
    $this->assertRaw('<option value="' . CURRENCY_SIGN_FORM_ELEMENT_CUSTOM_VALUE . '" selected="selected">');
    // Check if the sign element is set to a custom value.
    $this->assertFieldByXPath("//select[@name='sign[sign]']/option[@value='" . CURRENCY_SIGN_FORM_ELEMENT_CUSTOM_VALUE . "' and @selected='selected']");
    // Check if the custom sign input element has the custom sign as its value.
    $this->assertFieldByXPath("//input[@name='sign[sign_custom]' and @value='foobar']");

    // Test a non-existing currency.
    $values =  array(
      'sign[sign]' => '',
    );
    $this->drupalPost($path . '/ZZZ', $values, t('Submit'));
    $this->assertRaw("\$form_state['sign'] = ''");
  }
}
