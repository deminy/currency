<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\CurrencyLocaleFormElement.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency_locale form element.
 */
class CurrencyLocaleFormElement extends WebTestBase {

  public static $modules = array('currency_test');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'currency_locale form element',
      'group' => 'Currency',
    );
  }

  /**
   * Test validation.
   */
  function testValidation() {
    $path = 'currency_test-form-element-currency-locale';

    // Test valid values.
    $values =  array(
      'locale[locale][language_code]' => 'nl',
      'locale[locale][country_code]' => 'ZA',
    );
    $this->drupalPost($path, $values, t('Submit'));
    $this->assertRaw("\$form_state['locale'] = " . var_export('nl_ZA', TRUE));
  }
}
