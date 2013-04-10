<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Controller\CurrencyLocalePatternDeleteFormTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests Drupal\currency\Controller\CurrencyLocalePatternDeleteForm
 */
class CurrencyLocalePatternDeleteFormTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Controller\CurrencyLocalePatternDeleteForm',
      'group' => 'Currency',
    );
  }

  /**
   * Tests the form.
   */
  function testForm() {
    $user = $this->drupalCreateUser(array('currency.currency_locale_pattern.delete'));
    $this->drupalLogin($user);
    $currency_locale_pattern = entity_create('currency_locale_pattern', array(
      'locale' => 'zz_ZZ',
    ));
    $currency_locale_pattern->save();
    $this->drupalPost('admin/config/regional/currency_locale_pattern/' . $currency_locale_pattern->id() . '/delete', array(), t('Delete'));
    $this->assertFalse(entity_load_unchanged('currency_locale_pattern', $currency_locale_pattern->id()));
  }
}
