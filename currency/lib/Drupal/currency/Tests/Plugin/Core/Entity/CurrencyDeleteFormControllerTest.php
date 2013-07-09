<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\Core\Entity\CurrencyDeleteFormTest.
 */

namespace Drupal\currency\Tests\Plugin\Core\Entity;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the currency delete form.
 */
class CurrencyDeleteFormControllerTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Plugin\Core\Entity\CurrencyDeleteFormController',
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
    $this->assertFalse((bool) entity_load_unchanged('currency', $currency->id()));
  }
}
