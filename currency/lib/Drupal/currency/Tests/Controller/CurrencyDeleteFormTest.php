<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Controller\CurrencyDeleteFormTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency delete form.
 */
class CurrencyDeleteFormTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Controller\CurrencyDeleteForm',
      'group' => 'Currency',
    );
  }

  /**
   * Tests the form.
   */
  function testForm() {
    $user = $this->drupalCreateUser(array('currency.currency.delete'));
    $this->drupalLogin($user);
    $currency = entity_create('currency', array(
      'currencyCode' => 'ABC',
    ));
    $currency->save();
    $this->drupalPost('admin/config/regional/currency/' . $currency->id() . '/delete', array(), t('Delete'));
    $this->assertFalse(entity_load_unchanged('currency', $currency->id()));
  }
}
