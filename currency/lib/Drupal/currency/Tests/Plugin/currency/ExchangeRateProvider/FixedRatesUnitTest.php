<?php

/**
 * @file Contains
 * \Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider\FixedRatesUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider;

use Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates.
 */
class FixedRatesUnitTest extends UnitTestCase {

  /**
   * The config used for testing.
   *
   * @var \Drupal\Core\Config\Config|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $config;

  /**
   * The config factory used for testing.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * The math service.
   *
   * @var \Drupal\currency\MathInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $math;

  /**
   * The plugin under test.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomName();
    $plugin_definition = array();

    $this->configFactory = $this->getMockBuilder('\Drupal\Core\Config\ConfigFactory')
      ->disableOriginalConstructor()
      ->getMock();

    $this->math = $this->getMock('\Drupal\currency\MathInterface');

    $this->plugin = new FixedRates($configuration, $plugin_id, $plugin_definition, $this->configFactory, $this->math);
  }

  /**
   * Tests loadConfiguration().
   */
  public function testLoadConfiguration() {
    list($rates) = $this->prepareExchangeRates();
    $this->assertSame($rates, $this->plugin->loadConfiguration());
  }

  /**
   * Tests save() and load().
   */
  public function testLoad() {
    list($rates) = $this->prepareExchangeRates();
    $reverse_rate = '0.453780216';

    $this->math->expects($this->any())
      ->method('divide')
      ->with(1, 2.20371)
      ->will($this->returnValue($reverse_rate));

    // Test a rate that is stored in config.
    $this->assertSame($rates['EUR']['NLG'], $this->plugin->load('EUR', 'NLG')->getRate());

    // Test a reverse exchange rate.
    $this->assertSame($reverse_rate, $this->plugin->load('NLG', 'EUR')->getRate());

    // Test an unavailable exchange rate.
    $this->assertNull($this->plugin->load('NLG', 'UAH'));
  }

  /**
   * Tests loadMultiple().
   */
  public function testLoadMultiple() {
    list($rates) = $this->prepareExchangeRates();

    $this->math->expects($this->any())
      ->method('divide')
      ->with(1, $rates['EUR']['NLG'])
      ->will($this->returnValue($rates['NLG']['EUR']));

    $rates = array(
      'EUR' => array(
        'NLG' => $rates['EUR']['NLG'],
      ),
      'NLG' => array(
        'EUR' => $rates['NLG']['EUR'],
      ),
      'ABC' => array(
        'XXX' => NULL,
      ),
    );

    $returned_rates = $this->plugin->loadMultiple(array(
      // Test a rate that is stored in config.
      'EUR' => array('NLG'),
      // Test a reverse exchange rate.
      'NLG' => array('EUR'),
      // Test an unavailable exchange rate.
      'ABC' => array('XXX'),
    ));
    $this->assertSame($rates['EUR']['NLG'], $returned_rates['EUR']['NLG']->getRate());
    $this->assertSame($rates['NLG']['EUR'], $returned_rates['NLG']['EUR']->getRate());
    $this->assertNull($returned_rates['ABC']['XXX']);
  }

  /**
   * Tests save().
   */
  public function testSave() {
    $currency_code_from = $this->randomName(3);
    $currency_code_to = $this->randomName(3);
    $rate = mt_rand();
    list($rates, $rates_data) = $this->prepareExchangeRates();
    $rates[$currency_code_from][$currency_code_to] = $rate;
    $rates_data[] = array(
      'currency_code_from' => $currency_code_from,
      'currency_code_to' => $currency_code_to,
      'rate' => $rate,
    );

    $this->config->expects($this->once())
      ->method('set')
      ->with('rates', $rates_data);
    $this->config->expects($this->once())
      ->method('save');

    $this->plugin->save($currency_code_from, $currency_code_to, $rate);
  }

  /**
   * Tests delete().
   */
  function testDelete() {
    list($rates, $rates_data) = $this->prepareExchangeRates();
    unset($rates['EUR']['NLG']);

    $this->config->expects($this->once())
      ->method('set')
      ->with('rates', $rates_data);
    $this->config->expects($this->once())
      ->method('save');

    $this->plugin->delete('EUR', 'NLG');
  }

  /**
   * Stores random exchange rates in the mocked config and returns them.
   *
   * @return array
   *   An array of the same format as the return value of
   *   \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates::loadAll().
   */
  protected function prepareExchangeRates() {
    $rates = array(
      'EUR' => array(
        'NLG' => '2.20371',
      ),
      'NLG' => array(
        'EUR' => '0.453780216',
      ),
    );
    $rates_data = array();
    foreach ($rates as $currency_code_from => $currency_code_from_rates) {
      foreach ($currency_code_from_rates as $currency_code_to => $rate) {
        $rates_data[] = array(
          'currency_code_from' => $currency_code_from,
          'currency_code_to' => $currency_code_to,
          'rate' => $rate,
        );
      }
    }

    $this->config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $this->config->expects($this->any())
      ->method('get')
      ->with('rates')
      ->will($this->returnValue($rates_data));

    $this->configFactory->expects($this->any())
      ->method('get')
      ->with('currency.exchanger.fixed_rates')
      ->will($this->returnValue($this->config));

    return array($rates, $rates_data);
  }

}