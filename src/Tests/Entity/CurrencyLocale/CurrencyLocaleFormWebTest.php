<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\CurrencyLocale\CurrencyLocaleFormWebTest.
 */

namespace Drupal\currency\Tests\Entity\CurrencyLocale;

use Drupal\simpletest\WebTestBase;

/**
 * \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleForm web test.
 *
 * @group Currency
 */
class CurrencyLocaleFormWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Test Currency's UI.
   */
  function testUI() {
    $user = $this->drupalCreateUser(array('currency.currency_locale.view', 'currency.currency_locale.create', 'currency.currency_locale.update', 'currency.currency_locale.delete'));
    $this->drupalLogin($user);
    $path = 'admin/config/regional/currency-formatting/locale/add';

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
    $path = 'admin/config/regional/currency-formatting/locale/nl_UA';
    $this->drupalPostForm($path, array(), t('Save'));
    $this->assertUrl('admin/config/regional/currency-formatting/locale');
  }
}
