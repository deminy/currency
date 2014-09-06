<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Unit\ExchangeRateUnitTest.
 */

namespace Drupal\currency\Tests\Unit;

use Drupal\currency\ExchangeRate;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\ExchangeRate
 *
 * @group Currency
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
   *
   * @covers ::__construct
   */
  public function setUp() {
    $exchange_rate_provider_plugin_id = $this->randomMachineName();
    $timestamp = mt_rand();
    $source_currency_code = $this->randomMachineName(3);
    $destination_currency_code = $this->randomMachineName(3);
    $rate = mt_rand();
    $this->exchangeRate = new ExchangeRate($exchange_rate_provider_plugin_id, $timestamp, $source_currency_code, $destination_currency_code, $rate);
  }

  /**
   * @covers ::getDestinationCurrencyCode
   * @covers ::setDestinationCurrencyCode
   */
  public function testGetDestinationCurrencyCode() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setDestinationCurrencyCode($currency_code)));
    $this->assertSame($currency_code, $this->exchangeRate->getDestinationCurrencyCode());
  }

  /**
   * @covers ::getSourceCurrencyCode
   * @covers ::setSourceCurrencyCode
   */
  public function testGetSourceCurrencyCode() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setSourceCurrencyCode($currency_code)));
    $this->assertSame($currency_code, $this->exchangeRate->getSourceCurrencyCode());
  }

  /**
   * @covers ::getRate
   * @covers ::setRate
   */
  public function testGetRate() {
    $rate = mt_rand();
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setRate($rate)));
    $this->assertSame($rate, $this->exchangeRate->getRate());
  }

  /**
   * @covers ::getExchangeRateProviderPluginId
   * @covers ::setExchangeRateProviderPluginId
   */
  public function testGetExchangeRateProviderPluginId() {
    $plugin_id = $this->randomMachineName();
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setExchangeRateProviderPluginId($plugin_id)));
    $this->assertSame($plugin_id, $this->exchangeRate->getExchangeRateProviderPluginId());
  }

  /**
   * @covers ::getTimestamp
   * @covers ::setTimestamp
   */
  public function testGetTimestamp() {
    $timestamp = mt_rand();
    $this->assertSame(spl_object_hash($this->exchangeRate), spl_object_hash($this->exchangeRate->setTimestamp($timestamp)));
    $this->assertSame($timestamp, $this->exchangeRate->getTimestamp());
  }

}
