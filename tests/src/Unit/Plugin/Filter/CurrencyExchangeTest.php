<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\Filter\CurrencyExchangeTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Filter;

use BartFeenstra\CurrencyExchange\ExchangeRate;
use Drupal\currency\InputInterface;
use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface;
use Drupal\currency\Plugin\Filter\CurrencyExchange;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Filter\CurrencyExchange
 *
 * @group Currency
 */
class CurrencyExchangeTest extends UnitTestCase {

  /**
   * The exchange rate provider.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $exchangeRateProvider;

  /**
   * The input parser.
   *
   * @var \Drupal\currency\InputInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $input;

  /**
   * The plugin definiton.
   *
   * @var mixed[]
   */
  protected $pluginDefinition;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Plugin\Filter\CurrencyExchange|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $configuration = [];
    $plugin_id = $this->randomMachineName();
    $this->pluginDefinition = [
      'cache' => TRUE,
      'provider' => $this->randomMachineName(),
    ];

    $this->exchangeRateProvider = $this->getMock(ExchangeRateProviderInterface::class);

    $this->input = $this->getMock(InputInterface::class);

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->sut = new CurrencyExchange($configuration, $plugin_id, $this->pluginDefinition, $this->stringTranslation, $this->exchangeRateProvider, $this->input);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock(ContainerInterface::class);
    $map = [
      ['currency.input', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->input],
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
      ['currency.exchange_rate_provider', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->exchangeRateProvider],
    ];
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $sut = CurrencyExchange::create($container, [], '', $this->pluginDefinition);
    $this->assertInstanceOf(CurrencyExchange::class, $sut);
  }

  /**
   * @covers ::process
   * @covers ::processCallback
   */
  public function testProcess() {
    $currency_code_from = 'EUR';
    $currency_code_to = 'NLG';
    $rate = '2.20371';
    $exchange_rate = ExchangeRate::create($currency_code_from, $currency_code_to, $rate);

    $this->input->expects($this->any())
      ->method('parseAmount')
      ->will($this->returnArgument(0));

    $this->exchangeRateProvider->expects($this->any())
      ->method('load')
      ->with($currency_code_from, $currency_code_to)
      ->willReturn($exchange_rate);

    $langcode = $this->randomMachineName(2);
    $cache = TRUE;
    $cache_id = $this->randomMachineName();

    $tokens_valid = [
      '[currency:EUR:NLG]' => '2.20371',
      '[currency:EUR:NLG:1]' => '2.20371',
      '[currency:EUR:NLG:2]' => '4.40742',
    ];
    $tokens_invalid = [
      // Missing arguments.
      '[currency]',
      '[currency:]',
      '[currency::]',
      '[currency:EUR]',
      // Invalid currency code.
      '[currency:EUR:123]',
      '[currency:123:EUR]',
      // Invalid currency code and missing argument.
      '[currency:123]',
    ];

    foreach ($tokens_valid as $token => $replacement) {
      $this->assertSame($replacement, $this->sut->process($token, $langcode, $cache, $cache_id));
    }
    foreach ($tokens_invalid as $token) {
      $this->assertSame($token, $this->sut->process($token, $langcode, $cache, $cache_id));
    }
  }

  /**
   * @covers ::tips
   */
  public function testTips() {
    $this->assertInternalType('string', $this->sut->tips());
  }
}
