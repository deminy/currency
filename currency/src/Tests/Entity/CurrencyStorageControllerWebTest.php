<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\CurrencyStorageWebTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\currency\Entity\CurrencyInterface;
use Drupal\currency\UsageInterface;
use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Entity\CurrencyStorage.
 */
class CurrencyStorageWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Entity\CurrencyStorage web test',
      'group' => 'Currency',
    );
  }

  /**
   * Test CRUD functionality.
   */
  function testCRUD() {
    $currency_code = 'ABC';

    // Test that no currency with this currency code exists yet.
    $config = \Drupal::config('currency.currency.' . $currency_code);
    $this->assertIdentical($config->get('currencyNumber'), NULL);

    // Test creating a custom currency.
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = entity_create('currency', array());
    $this->assertTrue($currency instanceof CurrencyInterface);
    $this->assertTrue($currency->uuid());

    // Test saving a custom currency.
    $currency->setCurrencyCode($currency_code);
    $currency->setCurrencyNumber('123');
    $currency->save();
    $config = \Drupal::config('currency.currency.' . $currency_code);
    $this->assertEqual($config->get('currencyNumber'), '123');

    // Test loading a custom currency.
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency_loaded */
    $currency_loaded = entity_load('currency', $currency_code);
    $this->assertEqual($currency->getCurrencyNumber(), $currency_loaded->getCurrencyNumber());

    // Test loading a default currency.
    $currency_loaded = entity_load('currency', 'EUR');
    $this->assertTrue($currency_loaded instanceof CurrencyInterface);
    foreach ($currency_loaded->getUsages() as $usage) {
      $this->assertTrue($usage instanceof UsageInterface);
    }

    // Test deleting a custom currency.
    $currency->delete();
    $config = \Drupal::config('currency.currency.' . $currency_code);
    $this->assertIdentical($config->get('currencyNumber'), NULL);
  }
}