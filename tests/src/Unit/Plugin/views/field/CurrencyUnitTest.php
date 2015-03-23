<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\views\field\CurrencyUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\views\field;

use Drupal\currency\Plugin\views\field\Currency;
use Drupal\Tests\UnitTestCase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\views\field\Currency
 *
 * @group Currency
 */
class CurrencyUnitTest extends UnitTestCase {

  /**
   * The currency storage used for testing.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The handler under test.
   *
   * @var \Drupal\currency\Plugin\views\field\Currency
   */
  protected $handler;

  /**
   * The plugin configuration.
   *
   * @var mixed[]
   */
  protected $pluginConfiguration;

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
   * {@inheritdoc}
   */
  public function setUp() {
    $this->pluginConfiguration = [
      'currency_method' => 'label',
    ];
    $plugin_id = $this->randomMachineName();
    $this->pluginDefinition = [];

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->handler = new Currency($this->pluginConfiguration, $plugin_id, $this->pluginDefinition, $this->stringTranslation, $this->currencyStorage);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $plugin_id = $this->randomMachineName();
    $this->handler = new Currency($this->pluginConfiguration, $plugin_id, $this->pluginDefinition, $this->stringTranslation, $this->currencyStorage);
  }

  /**
   * @covers ::__construct
   *
   * @expectedException \InvalidArgumentException
   */
  public function testConstructWithoutMethod() {
    $plugin_id = $this->randomMachineName();

    $this->handler = new Currency([], $plugin_id, $this->pluginDefinition, $this->stringTranslation, $this->currencyStorage);
  }

  /**
   * @covers ::__construct
   *
   * @expectedException \InvalidArgumentException
   */
  public function testConstructWithNonExistentMethod() {
    $configuration = [
      'currency_method' => $this->randomMachineName(),
    ];
    $plugin_id = $this->randomMachineName();

    $this->filter = new Currency($configuration, $plugin_id, $this->pluginDefinition, $this->stringTranslation, $this->currencyStorage);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->atLeastOnce())
      ->method('getStorage')
      ->with('currency')
      ->willReturn($this->currencyStorage);

    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = [
      ['entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager],
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
    ];
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $filter = Currency::create($container, $this->pluginConfiguration, '', $this->pluginDefinition);
    $this->assertInstanceOf('\Drupal\currency\Plugin\views\field\Currency', $filter);
  }

  /**
   * @covers ::render
   */
  function testRenderWithExistingCurrency() {
    $currency_code = $this->randomMachineName();

    $currency_method_return_value = $this->randomMachineName();

    $field_alias = $this->randomMachineName();

    $this->handler->field_alias = $field_alias;

    $result_row = new ResultRow([
      $field_alias => $currency_code,
    ]);

    $currency = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency->expects($this->atLeastOnce())
      ->method($this->pluginConfiguration['currency_method'])
      ->willReturn($currency_method_return_value);

    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('load')
      ->with($currency_code)
      ->willReturn($currency);

    $this->assertSame($currency_method_return_value, $this->handler->render($result_row));
  }

  /**
   * @covers ::render
   */
  function testRenderWithNonExistingCurrency() {
    $currency_code = $this->randomMachineName();

    $field_alias = $this->randomMachineName();

    $this->handler->field_alias = $field_alias;

    $result_row = new ResultRow([
      $field_alias => $currency_code,
    ]);

    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('load')
      ->with($currency_code)
      ->willReturn(NULL);

    $this->assertInternalType('string', $this->handler->render($result_row));
  }

}
