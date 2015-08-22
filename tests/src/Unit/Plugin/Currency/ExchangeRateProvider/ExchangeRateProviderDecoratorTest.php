<?php

/**
 * @file Contains
 * \Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderDecoratorTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider;

use BartFeenstra\CurrencyExchange\ExchangeRateProviderInterface;
use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderDecorator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderDecorator
 *
 * @group Currency
 */
class ExchangeRateProviderDecoratorTest extends UnitTestCase {

  /**
   * The decorated exchange rate provider
   *
   * @var \BartFeenstra\CurrencyExchange\ExchangeRateProviderInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $exchangeRateProvider;

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->exchangeRateProvider = $this->getMock(ExchangeRateProviderInterface::class);

    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array();

    $this->sut = new ExchangeRateProviderDecorator($configuration, $plugin_id, $plugin_definition, $this->exchangeRateProvider);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array();

    $this->sut = new ExchangeRateProviderDecorator($configuration, $plugin_id, $plugin_definition, $this->exchangeRateProvider);
  }

  /**
   * @covers ::load
   */
  public function testLoad() {
    $source_currency_code = $this->randomMachineName();
    $destination_currency_code = $this->randomMachineName();
    $exchange_rate = mt_rand();

    $this->exchangeRateProvider->expects($this->once())
      ->method('load')
      ->with($source_currency_code, $destination_currency_code)
      ->willReturn($exchange_rate);

    $this->assertSame($exchange_rate, $this->sut->load($source_currency_code, $destination_currency_code));
  }

  /**
   * @covers ::loadMultiple
   */
  public function testLoadMultiple() {
    $source_currency_code_a = $this->randomMachineName();
    $destination_currency_code_a = $this->randomMachineName();
    $destination_currency_code_b = $this->randomMachineName();
    $exchange_rates = [
      $source_currency_code_a => [
        $destination_currency_code_a => mt_rand(),
        $destination_currency_code_b => mt_rand(),
      ],
    ];

    $this->exchangeRateProvider->expects($this->once())
      ->method('loadMultiple')
      ->with([
        $source_currency_code_a => [$destination_currency_code_a, $destination_currency_code_b],
      ])
      ->willReturn($exchange_rates);

    $this->assertSame($exchange_rates, $this->sut->loadMultiple([
      $source_currency_code_a => [$destination_currency_code_a, $destination_currency_code_b],
    ]));
  }

}
