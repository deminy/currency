<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\currency\exchanger\FixedRatesTest.
 */

namespace Drupal\currency\Tests\Plugin\currency\exchanger;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Plugin\currency\exchanger\FixedRates.
 */
class FixedRatesTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Overrides parent::getInfo().
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Plugin\currency\exchanger\FixedRates',
      'group' => 'Currency',
    );
  }

  /**
   * Gets the exchanger plugin.
   *
   * @return \Drupal\currency\Plugin\currency\exchanger\FixedRates
   */
  public function getPlugin() {
    return \Drupal::service('plugin.manager.currency.exchanger')->createInstance('currency_fixed_rates');
  }

  /**
   * Tests save() and loadAll().
   */
  public function testLoadAll() {
    $plugin = $this->getPlugin();
    $rate = '2.20371';
    $plugin->save('EUR', 'NLG', $rate);
    $plugin->save('UAH', 'USD', $rate);
    $rates = $plugin->loadAll();

    // Test the saved rates.
    $this->assertEqual($rates['EUR']['NLG'], $rate);
    $this->assertEqual($rates['UAH']['USD'], $rate);
  }

  /**
   * Tests save() and load().
   */
  public function testLoad() {
    $plugin = $this->getPlugin();
    $rate = '2.20371';
    $plugin->save('EUR', 'NLG', $rate);
    $this->assertEqual($plugin->load('EUR', 'NLG'), $rate);

    // Test a reverse exchange rate.
    $this->assertEqual($plugin->load('NLG', 'EUR'), '0.453780216');

    // Test an unavailable exchange rate.
    $this->assertFalse($plugin->load('NLG', 'UAH'));
  }

  /**
   * Tests delete().
   */
  function testDelete() {
    $plugin = $this->getPlugin();
    $plugin->save('EUR', 'NLG', '1');
    $plugin->save('EUR', 'UAH', '2');
    $plugin->delete('EUR', 'NLG');

    // Test the deleted exchange rate.
    $this->assertFalse($plugin->load('EUR', 'NLG'));

    // Test the reverse of the deleted exchange rate.
    $this->assertFalse($plugin->load('NLG', 'EUR'));

    // The an available exchange rate.
    $this->assertTrue($plugin->load('EUR', 'UAH'));
  }

  /**
   * Tests loadMultiple().
   */
  function testLoadMultiple() {
    $plugin = $this->getPlugin();
    $rate = '2.20371';
    $plugin->save('EUR', 'NLG', $rate);
    $plugin->save('UAH', 'USD', $rate);
    $rates = $plugin->loadMultiple(array(
      'EUR' => array('NLG'),
      'USD' => array('UAH'),
    ));

    // Test loaded rates.
    $this->assertEqual($rates['EUR']['NLG'], $rate);
    $this->assertEqual($rates['USD']['UAH'], '0.453780216');

    // Test a rate that was saved, but not loaded.
    $this->assertFalse(isset($rates['UAH']));
  }
}