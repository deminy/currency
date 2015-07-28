<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\ExchangeRateTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\currency\ExchangeRate;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\ExchangeRate
 *
 * @group Currency
 */
class ExchangeRateTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\currency\ExchangeRate
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $exchange_rate_provider_plugin_id = $this->randomMachineName();
    $timestamp = mt_rand();
    $source_currency_code = $this->randomMachineName(3);
    $destination_currency_code = $this->randomMachineName(3);
    $rate = mt_rand();
    $this->sut = new ExchangeRate($exchange_rate_provider_plugin_id, $timestamp, $source_currency_code, $destination_currency_code, $rate);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $exchange_rate_provider_plugin_id = $this->randomMachineName();
    $timestamp = mt_rand();
    $source_currency_code = $this->randomMachineName(3);
    $destination_currency_code = $this->randomMachineName(3);
    $rate = mt_rand();
    $this->sut = new ExchangeRate($exchange_rate_provider_plugin_id, $timestamp, $source_currency_code, $destination_currency_code, $rate);
  }

  /**
   * @covers ::getDestinationCurrencyCode
   * @covers ::setDestinationCurrencyCode
   */
  public function testGetDestinationCurrencyCode() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame(spl_object_hash($this->sut), spl_object_hash($this->sut->setDestinationCurrencyCode($currency_code)));
    $this->assertSame($currency_code, $this->sut->getDestinationCurrencyCode());
  }

  /**
   * @covers ::getSourceCurrencyCode
   * @covers ::setSourceCurrencyCode
   */
  public function testGetSourceCurrencyCode() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame(spl_object_hash($this->sut), spl_object_hash($this->sut->setSourceCurrencyCode($currency_code)));
    $this->assertSame($currency_code, $this->sut->getSourceCurrencyCode());
  }

  /**
   * @covers ::getRate
   * @covers ::setRate
   */
  public function testGetRate() {
    $rate = mt_rand();
    $this->assertSame(spl_object_hash($this->sut), spl_object_hash($this->sut->setRate($rate)));
    $this->assertSame($rate, $this->sut->getRate());
  }

  /**
   * @covers ::getExchangeRateProviderPluginId
   * @covers ::setExchangeRateProviderPluginId
   */
  public function testGetExchangeRateProviderPluginId() {
    $plugin_id = $this->randomMachineName();
    $this->assertSame(spl_object_hash($this->sut), spl_object_hash($this->sut->setExchangeRateProviderPluginId($plugin_id)));
    $this->assertSame($plugin_id, $this->sut->getExchangeRateProviderPluginId());
  }

  /**
   * @covers ::getTimestamp
   * @covers ::setTimestamp
   */
  public function testGetTimestamp() {
    $timestamp = mt_rand();
    $this->assertSame(spl_object_hash($this->sut), spl_object_hash($this->sut->setTimestamp($timestamp)));
    $this->assertSame($timestamp, $this->sut->getTimestamp());
  }

}
