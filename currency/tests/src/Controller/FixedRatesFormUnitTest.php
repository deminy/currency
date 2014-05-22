<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\FixedRatesFormUnitTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\currency\Controller\FixedRatesForm;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\FixedRatesForm
 */
class FixedRatesFormUnitTest extends UnitTestCase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected  $configFactory;

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\FixedRatesForm
   */
  protected $controller;

  /**
   * The currency exchange rate provider manager.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyExchangeRateProviderManager;

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Controller\FixedRatesForm unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->configFactory = $this->getMock('\Drupal\Core\Config\ConfigFactoryInterface');

    $this->currencyExchangeRateProviderManager = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface');

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->controller = new FixedRatesForm($this->configFactory, $this->stringTranslation, $this->currencyStorage, $this->currencyExchangeRateProviderManager);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->will($this->returnValue($this->currencyStorage));

    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('config.factory', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->configFactory),
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager),
      array('plugin.manager.currency.exchange_rate_provider', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->currencyExchangeRateProviderManager),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = FixedRatesForm::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\FixedRatesForm', $form);
  }

  /**
   * @covers ::getFormId
   */
  public function testGetFormId() {
    $this->assertSame('currency_exchange_rate_provider_fixed_rates', $this->controller->getFormId());
  }

}
