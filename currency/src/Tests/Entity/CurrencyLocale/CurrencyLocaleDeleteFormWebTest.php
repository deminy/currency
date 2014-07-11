<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\Currency\CurrencyLocaleDeleteFormWebTest.
 */

namespace Drupal\currency\Tests\Entity\CurrencyLocale;

use Drupal\simpletest\WebTestBase;

/**
 * Tests Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleDeleteForm
 */
class CurrencyLocaleDeleteFormWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleDeleteForm web test',
      'group' => 'Currency',
    );
  }

  /**
   * Tests the form.
   */
  function testForm() {
    $user = $this->drupalCreateUser(array('currency.currency_locale.delete'));
    $this->drupalLogin($user);
    $currency_locale = entity_create('currency_locale', array(
      'locale' => 'zz_ZZ',
    ));
    $currency_locale->save();
    $this->drupalPostForm('admin/config/regional/currency-formatting/locale/' . $currency_locale->id() . '/delete', array(), t('Delete'));
    $this->assertFalse(entity_load_unchanged('currency_locale', $currency_locale->id()));
  }
}
