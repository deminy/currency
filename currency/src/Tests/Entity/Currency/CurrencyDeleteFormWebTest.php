<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\Currency\CurrencyDeleteFormWebTest.
 */

namespace Drupal\currency\Tests\Entity\Currency;

use Drupal\simpletest\WebTestBase;

/**
 * \Drupal\currency\Entity\CurrencyDeleteForm web test.
 *
 * @group Currency
 */
class CurrencyDeleteFormWebTest extends WebTestBase {

  public static $modules = array('currency');

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
    $this->drupalPostForm('admin/config/regional/currency/' . $currency->id() . '/delete', array(), t('Delete'));
    $this->assertFalse((bool) entity_load_unchanged('currency', $currency->id()));
  }
}
