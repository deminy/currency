<?php

/**
 * @file Contains
 * \Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider\HistoricalRatesTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider;

use Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates
 *
 * @group Currency
 */
class HistoricalRatesTest extends UnitTestCase {

  /**
   * The plugin under test.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array();

    $this->plugin = new HistoricalRates($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * @covers ::load
   */
  public function testLoad() {
    $expected_rates = $this->prepareExchangeRates();

    $reverse_rate = '0.511291';

    // Test rates that are stored in config.
    $this->assertSame($expected_rates['EUR']['NLG'], $this->plugin->load('EUR', 'NLG')->getRate());
    $this->assertSame($expected_rates['EUR']['DEM'], $this->plugin->load('EUR', 'DEM')->getRate());

    // Test a rate that is calculated on-the-fly.
    $this->assertSame($reverse_rate, $this->plugin->load('DEM', 'EUR')->getRate());

    // Test an unavailable exchange rate.
    $this->assertNull($this->plugin->load('UAH', 'EUR'));
    $this->assertNull($this->plugin->load('EUR', 'UAH'));
  }

  /**
   * @covers ::loadMultiple
   */
  public function testLoadMultiple() {
    $expected_rates = $this->prepareExchangeRates();

    $returned_rates = $this->plugin->loadMultiple(array(
      // Test a rate that is stored in config.
      'EUR' => array('NLG'),
      // Test a reverse exchange rate.
      'NLG' => array('EUR'),
      // Test an unavailable exchange rate.
      'ABC' => array('XXX'),
    ));

    $this->assertSame($expected_rates['EUR']['NLG'], $returned_rates['EUR']['NLG']->getRate());
    $this->assertSame($expected_rates['NLG']['EUR'], $returned_rates['NLG']['EUR']->getRate());
    $this->assertNull($returned_rates['ABC']['XXX']);
  }

  /**
   * Stores random exchange rates in the mocked config and returns them.
   *
   * @return array
   */
  protected function prepareExchangeRates() {
    $rates = array(
      'EUR' => array(
        'DEM' => '1.95583',
        'NLG' => '2.20371',
      ),
      'NLG' => array(
        'EUR' => '0.453780',
      ),
    );

    return $rates;
  }
}
