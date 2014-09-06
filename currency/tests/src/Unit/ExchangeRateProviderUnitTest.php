<?php

/**
 * @file Contains \Drupal\Tests\currency\Unit\ExchangeRateProviderUnitTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\currency\ExchangeRate;
use Drupal\currency\ExchangeRateProvider;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\ExchangeRateProvider
 *
 * @group Currency
 */
class ExchangeRateProviderUnitTest extends UnitTestCase {

  /**
   * The configuration factory used for testing.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * The exchange rate provider under test.
   *
   * @var \Drupal\currency\ExchangeRateProvider
   */
  protected $exchangeRateProvider;

  /**
   * The currency exchanger plugin manager used for testing.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyExchangeRateProviderManager;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->configFactory = $this->getMockBuilder('\Drupal\Core\Config\ConfigFactory')
      ->disableOriginalConstructor()
      ->getMock();

    $this->currencyExchangeRateProviderManager = $this->getMock('\Drupal\Component\Plugin\PluginManagerInterface');

    $this->exchangeRateProvider = new ExchangeRateProvider($this->currencyExchangeRateProviderManager, $this->configFactory);
  }

  /**
   * @covers ::loadConfiguration
   */
  public function testLoadConfiguration() {
    $plugin_id_a = $this->randomMachineName();
    $plugin_id_b = $this->randomMachineName();

    $plugin_definitions = array(
      $plugin_id_a => array(),
      $plugin_id_b => array(),
    );

    $config_value = array(
      array(
        'plugin_id' => $plugin_id_b,
        'status' => TRUE,
      ),
    );

    $this->currencyExchangeRateProviderManager->expects($this->once())
      ->method('getDefinitions')
      ->will($this->returnValue($plugin_definitions));

    $config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->once())
      ->method('get')
      ->with('plugins')
      ->will($this->returnValue($config_value));

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('currency.exchange_rate_provider')
      ->will($this->returnValue($config));

    $configuration = $this->exchangeRateProvider->loadConfiguration();
    $expected = array(
      $plugin_id_b => TRUE,
      $plugin_id_a => FALSE,
    );
    $this->assertSame($expected, $configuration);
  }

  /**
   * @covers ::saveConfiguration
   */
  public function testSaveConfiguration() {
    $configuration = array(
      'currency_historical_rates' => TRUE,
      'currency_fixed_rates' => TRUE,
      'foo' => FALSE,
    );
    $configuration_data = array(
      array(
        'plugin_id' => 'currency_historical_rates',
        'status' => TRUE,
      ),
      array(
        'plugin_id' => 'currency_fixed_rates',
        'status' => TRUE,
      ),
      array(
        'plugin_id' => 'foo',
        'status' => FALSE,
      ),
    );

    $config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->once())
      ->method('set')
      ->with('plugins', $configuration_data);
    $config->expects($this->once())
      ->method('save');

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('currency.exchange_rate_provider')
      ->will($this->returnValue($config));

    $this->exchangeRateProvider->saveConfiguration($configuration);
  }

  /**
   * @covers ::load
   */
  public function testLoad() {
    $currency_code_from = 'EUR';
    $currency_code_to = 'NLG';
    $rate = '2.20371';

    $plugin_a = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface');
    $plugin_a->expects($this->once())
      ->method('load')
      ->with($currency_code_from, $currency_code_to)
      ->will($this->returnValue(NULL));

    $plugin_b = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface');
    $plugin_b->expects($this->once())
      ->method('load')
      ->with($currency_code_from, $currency_code_to)
      ->will($this->returnValue($rate));

    /** @var \Drupal\currency\ExchangeRateProvider|\PHPUnit_Framework_MockObject_MockObject $exchange_rate_provider */
    $exchange_rate_provider = $this->getMockBuilder('\Drupal\currency\ExchangeRateProvider')
      ->setConstructorArgs(array($this->currencyExchangeRateProviderManager, $this->configFactory))
      ->setMethods(array('getPlugins'))
      ->getMock();
    $exchange_rate_provider->expects($this->once())
      ->method('getPlugins')
      ->will($this->returnValue(array($plugin_a, $plugin_b)));

    $this->assertSame($rate, $exchange_rate_provider->load($currency_code_from, $currency_code_to));
  }

  /**
   * @covers ::load
   */
  public function testLoadWithIdenticalCurrencies() {
    $currency_code_from = 'EUR';
    $currency_code_to = 'EUR';

    $rate = $this->exchangeRateProvider->load($currency_code_from, $currency_code_to);
    $this->assertInstanceOf('\Drupal\currency\ExchangeRate', $rate);
    $this->assertSame(1, $rate->getRate());
  }

  /**
   * @covers ::load
   */
  public function testLoadWithoutPlugins() {
    $currency_code_from = $this->randomMachineName();
    $currency_code_to = $this->randomMachineName();

    /** @var \Drupal\currency\ExchangeRateProvider|\PHPUnit_Framework_MockObject_MockObject $exchange_rate_provider */
    $exchange_rate_provider = $this->getMockBuilder('\Drupal\currency\ExchangeRateProvider')
      ->setConstructorArgs(array($this->currencyExchangeRateProviderManager, $this->configFactory))
      ->setMethods(array('getPlugins'))
      ->getMock();
    $exchange_rate_provider->expects($this->once())
      ->method('getPlugins')
      ->will($this->returnValue(array()));

    $this->assertNull($exchange_rate_provider->load($currency_code_from, $currency_code_to));
  }

  /**
   * @covers ::loadMultiple
   */
  public function testLoadMultiple() {
    $currency_code_from_a = 'EUR';
    $currency_code_to_a = 'NLG';
    $rate_a = '2.20371';
    $currency_code_from_b = 'NLG';
    $currency_code_to_b = 'EUR';
    $rate_b = '0.453780216';

    // Convert both currencies to each other and themselves.
    $requested_rates_provider = array(
      $currency_code_from_a => array($currency_code_to_a, $currency_code_from_a),
      $currency_code_from_b => array($currency_code_to_b, $currency_code_from_b),
    );
    // By the time plugin A will be called, the identical source and destination
    // currencies will have been processed.
    $requested_rates_plugin_a = array(
      $currency_code_from_a => array($currency_code_to_a),
      $currency_code_from_b => array($currency_code_to_b),
    );
    // By the time plugin B will be called, the 'A' source and destination
    // currencies will have been processed.
    $requested_rates_plugin_b = array(
      $currency_code_from_a => array(),
      $currency_code_from_b => array($currency_code_to_b),
    );

    $plugin_a = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface');
    $returned_rates_a = array(
      $currency_code_from_a => array(
        $currency_code_to_a => new ExchangeRate(NULL, NULL, $currency_code_from_a, $currency_code_to_a, $rate_a),
      ),
      $currency_code_from_b => array(
        $currency_code_to_b => NULL,
      ),
    );
    $plugin_a->expects($this->once())
      ->method('loadMultiple')
      ->with($requested_rates_plugin_a)
      ->will($this->returnValue($returned_rates_a));

    $plugin_b = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface');
    $returned_rates_b = array(
      $currency_code_from_a => array(
        $currency_code_to_a => NULL,
      ),
      $currency_code_from_b => array(
        $currency_code_to_b => new ExchangeRate(NULL, NULL, $currency_code_from_a, $currency_code_to_a, $rate_b),
      ),
    );
    $plugin_b->expects($this->once())
      ->method('loadMultiple')
      ->with($requested_rates_plugin_b)
      ->will($this->returnValue($returned_rates_b));

    /** @var \Drupal\currency\ExchangeRateProvider|\PHPUnit_Framework_MockObject_MockObject $exchange_rate_provider */
    $exchange_rate_provider = $this->getMockBuilder('\Drupal\currency\ExchangeRateProvider')
      ->setConstructorArgs(array($this->currencyExchangeRateProviderManager, $this->configFactory))
      ->setMethods(array('getPlugins'))
      ->getMock();
    $exchange_rate_provider->expects($this->once())
      ->method('getPlugins')
      ->will($this->returnValue(array($plugin_a, $plugin_b)));

    $returned_rates = $exchange_rate_provider->loadMultiple($requested_rates_provider);
    $this->assertSame($returned_rates_a[$currency_code_from_a][$currency_code_to_a], $returned_rates[$currency_code_from_a][$currency_code_to_a]);
    $this->assertSame(1, $returned_rates[$currency_code_from_a][$currency_code_from_a]->getRate());
    $this->assertSame($returned_rates_b[$currency_code_from_b][$currency_code_to_b], $returned_rates[$currency_code_from_b][$currency_code_to_b]);
    $this->assertSame(1, $returned_rates[$currency_code_from_b][$currency_code_from_b]->getRate());
  }

  /**
   * @covers ::getPlugins
   *
   * @depends testLoadConfiguration
   */
  public function testGetPlugins() {
    $configuration = array(
      'foo' => TRUE,
      $this->randomMachineName() => FALSE,
      'bar' => TRUE,
      'baz' => FALSE,
    );

    /** @var \Drupal\currency\ExchangeRateProvider|\PHPUnit_Framework_MockObject_MockObject $exchange_rate_provider */
    $exchange_rate_provider = $this->getMockBuilder('\Drupal\currency\ExchangeRateProvider')
      ->setConstructorArgs(array($this->currencyExchangeRateProviderManager, $this->configFactory))
      ->setMethods(array('loadConfiguration'))
      ->getMock();
    $exchange_rate_provider->expects($this->once())
      ->method('loadConfiguration')
      ->will($this->returnValue($configuration));

    $plugin_foo = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface');
    $plugin_bar = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface');

    $map = array(
      array('foo', array(), $plugin_foo),
      array('bar', array(), $plugin_bar),
    );

    $this->currencyExchangeRateProviderManager->expects($this->exactly(2))
      ->method('createInstance')
      ->will($this->returnValueMap($map));

    $method = new \ReflectionMethod($exchange_rate_provider, 'getPlugins');
    $method->setAccessible(TRUE);

    $plugins = $method->invoke($exchange_rate_provider);
    $expected = array(
      'foo' => $plugin_foo,
      'bar' => $plugin_bar,
    );
    $this->assertSame($expected, $plugins);
  }
}
