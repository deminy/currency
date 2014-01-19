<?php

/**
 * @file Contains
 * \Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider\FixedRatesTest.
 */

namespace Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider;

use Drupal\currency\ExchangeRateInterface;
use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates.
 */
class FixedRatesTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates web test',
      'group' => 'Currency',
    );
  }

  /**
   * Gets the exchanger plugin.
   *
   * @return \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates
   */
  public function getPlugin() {
    return \Drupal::service('plugin.manager.currency.exchange_rate_provider')->createInstance('currency_fixed_rates');
  }

  /**
   * Tests save() and loadConfiguration().
   */
  public function testLoadAll() {
    $plugin = $this->getPlugin();
    $rate = '2.20371';
    $plugin->save('EUR', 'NLG', $rate);
    $plugin->save('UAH', 'USD', $rate);
    $rates = $plugin->loadConfiguration();

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
    $this->assertEqual($plugin->load('EUR', 'NLG')->getRate(), $rate);

    // Test a reverse exchange rate.
    $this->assertEqual($plugin->load('NLG', 'EUR')->getRate(), '0.453780216');

    // Test an unavailable exchange rate.
    $this->assertNull($plugin->load('NLG', 'UAH'));
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
    $this->assertNull($plugin->load('EUR', 'NLG'));

    // Test the reverse of the deleted exchange rate.
    $this->assertNull($plugin->load('NLG', 'EUR'));

    // The an available exchange rate.
    $this->assertTrue($plugin->load('EUR', 'UAH') instanceof ExchangeRateInterface);
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
    $this->assertEqual($rates['EUR']['NLG']->getRate(), $rate);
    $this->assertEqual($rates['USD']['UAH']->getRate(), '0.453780216');

    // Test a rate that was saved, but not loaded.
    $this->assertFalse(isset($rates['UAH']));
  }
}