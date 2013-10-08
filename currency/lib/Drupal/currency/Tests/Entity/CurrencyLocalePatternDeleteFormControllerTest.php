<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Entity\CurrencyLocalePatternDeleteFormTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\simpletest\WebTestBase;

/**
 * Tests Drupal\currency\CurrencyLocalePatternDeleteForm
 */
class CurrencyLocalePatternDeleteFormControllerTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Entity\CurrencyLocalePatternDeleteFormController',
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
    $this->drupalPostForm('admin/config/regional/currency_locale_pattern/' . $currency_locale_pattern->id() . '/delete', array(), t('Delete'));
    $this->assertFalse(entity_load_unchanged('currency_locale_pattern', $currency_locale_pattern->id()));
  }
}
