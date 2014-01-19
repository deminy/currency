<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\FixedRatesOverviewUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Currency\AmountFormatter;

use Drupal\currency\Controller\FixedRatesOverview;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\Controller\FixedRatesOverview.
 */
class FixedRatesOverviewUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\FixedRatesOverview
   */
  protected $controller;

  /**
   * The currency amount formatter manager used for testing.
   *
   * @var \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyAmountFormatterManager;

  /**
   * The currency exchange rate provider manager used for testing.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyExchangeRateProviderManager;

  /**
   * The currency storage used for testing.
   *
   * @var \Drupal\Core\Entity\EntityStorageControllerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The translation manager used for testing.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $translationManager;

  /**
   * The URL generator used for testing.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Controller\FixedRatesOverview unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageControllerInterface');

    $this->currencyAmountFormatterManager = $this->getMock('\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface');

    $this->currencyExchangeRateProviderManager = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface');

    $this->translationManager = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->urlGenerator = $this->getMock('\Drupal\Core\Routing\UrlGeneratorInterface');

    $this->controller = new FixedRatesOverview($this->translationManager, $this->urlGenerator, $this->currencyStorage, $this->currencyAmountFormatterManager, $this->currencyExchangeRateProviderManager);
  }

  /**
   * @covers \Drupal\currency\Controller\FixedRatesOverview::overview())
   */
  public function testOverview() {
    $currency_code_from = 'EUR';
    $currency_code_to = 'NLG';
    $rate = '2.20371';

    $currency_from = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_from->expects($this->once())
      ->method('label');

    $currency_to = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_to->expects($this->once())
      ->method('label');

    $map = array(
      array($currency_code_from, $currency_from),
      array($currency_code_to, $currency_to),
    );
    $this->currencyStorage->expects($this->any())
      ->method('load')
      ->will($this->returnValueMap($map));

    $rates_configuration = array(
      $currency_code_from => array(
        $currency_code_to => $rate,
      ),
    );
    $fixed_rates = $this->getMockBuilder('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates')
      ->disableOriginalConstructor()
      ->getMock();
    $fixed_rates->expects($this->once())
      ->method('loadConfiguration')
      ->will($this->returnValue($rates_configuration));

    $this->currencyExchangeRateProviderManager->expects($this->once())
      ->method('createInstance')
      ->with('currency_fixed_rates')
      ->will($this->returnValue($fixed_rates));

    $this->urlGenerator->expects($this->once())
      ->method('generateFromRoute')
      ->with('currency_exchange_rates_provider_fixed_rates_add');

    $amount_formatter = $this->getMock('\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface');
    $amount_formatter->expects($this->once())
      ->method('formatAmount')
      ->with($currency_to, $rate);

    $this->currencyAmountFormatterManager->expects($this->once())
      ->method('getDefaultPlugin')
      ->will($this->returnValue($amount_formatter));

    $build = $this->controller->overview();
    $this->assertInternalType('array', $build);
  }
}
