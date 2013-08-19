<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Entity\CurrencyLocalePatternFormControllerTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Drupal\currency\CurrencyLocalePatternFormController.
 */
class CurrencyLocalePatternFormControllerTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\CurrencyLocalePatternFormController',
      'group' => 'Currency',
    );
  }

  /**
   * Test Currency's UI.
   */
  function testUI() {
    $user = $this->drupalCreateUser(array('currency.currency_locale_pattern.view', 'currency.currency_locale_pattern.create', 'currency.currency_locale_pattern.update', 'currency.currency_locale_pattern.delete'));
    $this->drupalLogin($user);
    $path = 'admin/config/regional/currency_locale_pattern/add';

    // Test valid values.
    $valid_values = array(
      'language_code' => 'nl',
      'country_code' => 'UA',
      'pattern' => 'foo',
      'decimal_separator' => '1',
      'grouping_separator' => 'foobar',
    );
    $this->drupalPost($path, $valid_values, t('Save'));
    $currency = entity_load('currency_locale_pattern', 'nl_UA');
    $this->assertTrue($currency);

    // Edit and save an existing currency.
    $path = 'admin/config/regional/currency_locale_pattern/nl_UA/edit';
    $this->drupalPost($path, array(), t('Save'));
    $this->assertUrl('admin/config/regional/currency_locale_pattern');
  }
}
