<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\ExchangeRateProviderFormUnitTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\currency\Controller\ExchangeRateProviderForm;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\ExchangeRateProviderForm
 *
 * @group Currency
 */
class ExchangeRateProviderFormUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\ExchangeRateProviderForm
   */
  protected $controller;

  /**
   * The currency exchange rate provider manager.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface
   */
  protected $currencyExchangeRateProviderManager;

  /**
   * The currency exchange rate provider.
   *
   * @var \Drupal\currency\ExchangeRateProviderInterface
   */
  protected $exchangeRateProvider;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->currencyExchangeRateProviderManager = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface');

    $this->exchangeRateProvider = $this->getMock('\Drupal\currency\ExchangeRateProviderInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->controller = new ExchangeRateProviderForm($this->stringTranslation, $this->exchangeRateProvider, $this->currencyExchangeRateProviderManager);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('plugin.manager.currency.exchange_rate_provider', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->currencyExchangeRateProviderManager),
      array('currency.exchange_rate_provider', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->exchangeRateProvider),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = ExchangeRateProviderForm::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\ExchangeRateProviderForm', $form);
  }

  /**
   * @covers ::getFormId
   */
  public function testGetFormId() {
    $this->assertSame('currency_exchange_rate_provider', $this->controller->getFormId());
  }

}
