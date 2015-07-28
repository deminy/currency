<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\CurrencyLocale\CurrencyLocaleListBuilderTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\CurrencyLocale;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleListBuilder;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleListBuilder
 *
 * @group Currency
 */
class CurrencyLocaleListBuilderTest extends UnitTestCase {

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityStorage;

  /**
   * The entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityType;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * The form under test.
   *
   * @var \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleListBuilder
   */
  protected $form;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->entityStorage = $this->getMock(EntityStorageInterface::class);

    $this->entityType = $this->getMock(EntityTypeInterface::class);

    $this->moduleHandler = $this->getMock(ModuleHandlerInterface::class);

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->form = new CurrencyLocaleListBuilder($this->entityType, $this->entityStorage, $this->stringTranslation, $this->moduleHandler);
  }

  /**
   * @covers ::createInstance
   * @covers ::__construct
   */
  function testCreateInstance() {
    $this->entityType->expects($this->any())
      ->method('id')
      ->will($this->returnValue('currency'));

    $entity_manager = $this->getMock(EntityManagerInterface::class);
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->will($this->returnValue($this->entityStorage));

    $container = $this->getMock(ContainerInterface::class);
    $map = array(
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager),
      array('module_handler', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->moduleHandler),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = CurrencyLocaleListBuilder::createInstance($container, $this->entityType);
    $this->assertInstanceOf(CurrencyLocaleListBuilder::class, $form);
  }

  /**
   * @covers ::buildHeader
   */
  function testBuildHeader() {
    $header = $this->form->buildHeader();
    $expected = array(
      'label' => 'Locale',
      'operations' => 'Operations',
    );
    $this->assertSame($expected, $header);
  }

  /**
   * @covers ::buildRow
   */
  function testBuildRow() {
    $entity_label = $this->randomMachineName();

    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);
    $currency_locale->expects($this->any())
      ->method('label')
      ->will($this->returnValue($entity_label));

    $this->moduleHandler->expects($this->any())
      ->method('invokeAll')
      ->will($this->returnValue(array()));

    $row = $this->form->buildRow($currency_locale);
    $expected = array(
      'label' => $entity_label,
      'operations' => array(
        'data' => array(
          '#type' => 'operations',
          '#links' => array(),
        ),
      ),
    );
    $this->assertSame($expected, $row);
  }

}
