<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\CurrencyLocaleFormControllerWebTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Drupal\currency\CurrencyLocaleFormController.
 */
class CurrencyLocaleFormControllerWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\CurrencyLocaleFormController web test',
      'group' => 'Currency',
    );
  }

  /**
   * Test Currency's UI.
   */
  function testUI() {
    $user = $this->drupalCreateUser(array('currency.currency_locale.view', 'currency.currency_locale.create', 'currency.currency_locale.update', 'currency.currency_locale.delete'));
    $this->drupalLogin($user);
    $path = 'admin/config/regional/currency_locale/add';

    // Test valid values.
    $valid_values = array(
      'language_code' => 'nl',
      'country_code' => 'UA',
      'pattern' => 'foo',
      'decimal_separator' => '1',
      'grouping_separator' => 'foobar',
    );
    $this->drupalPostForm($path, $valid_values, t('Save'));
    $currency = entity_load('currency_locale', 'nl_UA');
    $this->assertTrue($currency);

    // Edit and save an existing currency.
    $path = 'admin/config/regional/currency_locale/nl_UA/edit';
    $this->drupalPostForm($path, array(), t('Save'));
    $this->assertUrl('admin/config/regional/currency_locale');
  }
}
