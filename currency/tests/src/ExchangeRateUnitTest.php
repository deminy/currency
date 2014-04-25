<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\ExchangeRateUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Currency\AmountFormatter;

use Drupal\currency\ExchangeRate;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\ExchangeRate.
 */
class ExchangeRateUnitTest extends UnitTestCase {

  /**
   * The exchange rate under test.
   *
   * @var \Drupal\currency\ExchangeRate
   */
  protected $exchangeRate;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\ExchangeRate unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $exchange_rate_provider_plugin_id = $this->randomName();
    $timestamp = mt_rand();
    $source_currency_code = $this->randomName(3);
    $destination_currency_code = $this->randomName(3);
    $rate = mt_rand();
    $this->exchangeRate = new ExchangeRate($exchange_rate_provider_plugin_id, $timestamp, $source_currency_code, $destination_currency_code, $rate);
  }

  /**
   * @covers \Drupal\currency\ExchangeRate::getDestinationCurrencyCode()
   * @covers \Drupal\currency\ExchangeRate::setDestinationCurrencyCode()
   */
  public function testGetDestinationCurrencyCode() {
    $currency_code = $this->randomName(3);
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setDestinationCurrencyCode($currency_code)));
    $this->assertSame($currency_code, $this->exchangeRate->getDestinationCurrencyCode());
  }

  /**
   * @covers \Drupal\currency\ExchangeRate::getSourceCurrencyCode()
   * @covers \Drupal\currency\ExchangeRate::setSourceCurrencyCode()
   */
  public function testGetSourceCurrencyCode() {
    $currency_code = $this->randomName(3);
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setSourceCurrencyCode($currency_code)));
    $this->assertSame($currency_code, $this->exchangeRate->getSourceCurrencyCode());
  }

  /**
   * @covers \Drupal\currency\ExchangeRate::getRate()
   * @covers \Drupal\currency\ExchangeRate::setRate()
   */
  public function testGetRate() {
    $rate = mt_rand();
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setRate($rate)));
    $this->assertSame($rate, $this->exchangeRate->getRate());
  }

  /**
   * @covers \Drupal\currency\ExchangeRate::getExchangeRateProviderPluginId()
   * @covers \Drupal\currency\ExchangeRate::setExchangeRateProviderPluginId()
   */
  public function testGetExchangeRateProviderPluginId() {
    $plugin_id = $this->randomName();
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setExchangeRateProviderPluginId($plugin_id)));
    $this->assertSame($plugin_id, $this->exchangeRate->getExchangeRateProviderPluginId());
  }

  /**
   * @covers \Drupal\currency\ExchangeRate::getTimestamp()
   * @covers \Drupal\currency\ExchangeRate::setTimestamp()
   */
  public function testGetTimestamp() {
    $timestamp = mt_rand();
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setTimestamp($timestamp)));
    $this->assertSame($timestamp, $this->exchangeRate->getTimestamp());
  }
}
