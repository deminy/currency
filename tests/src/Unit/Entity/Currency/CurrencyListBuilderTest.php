<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\Currency\CurrencyListBuilderTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\Currency;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\currency\Entity\Currency\CurrencyListBuilder;
use Drupal\currency\Entity\CurrencyInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Entity\Currency\CurrencyListBuilder
 *
 * @group Currency
 */
class CurrencyListBuilderTest extends UnitTestCase {

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
   * @var \Drupal\currency\Entity\Currency\CurrencyListBuilder
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

    $this->form = new CurrencyListBuilder($this->entityType, $this->entityStorage, $this->stringTranslation, $this->moduleHandler);
  }

  /**
   * @covers ::createInstance
   * @covers ::__construct
   */
  function testCreateInstance() {
    $entity_manager = $this->getMock(EntityManagerInterface::class);
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->willReturn($this->entityStorage);

    $container = $this->getMock(ContainerInterface::class);
    $map = array(
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager),
      array('module_handler', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->moduleHandler),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $form = CurrencyListBuilder::createInstance($container, $this->entityType);
    $this->assertInstanceOf(CurrencyListBuilder::class, $form);
  }

  /**
   * @covers ::buildHeader
   */
  function testBuildHeader() {
    $header = $this->form->buildHeader();
    $expected = array(
      'id' => 'Currency code',
      'label' => 'Name',
      'operations' => 'Operations',
    );
    $this->assertSame($expected, $header);
  }

  /**
   * @covers ::buildRow
   */
  function testBuildRow() {
    $entity_id = $this->randomMachineName();
    $entity_label = $this->randomMachineName();

    $currency = $this->getMock(CurrencyInterface::class);
    $currency->expects($this->any())
      ->method('id')
      ->willReturn($entity_id);
    $currency->expects($this->any())
      ->method('label')
      ->willReturn($entity_label);

    $this->moduleHandler->expects($this->any())
      ->method('invokeAll')
      ->willReturn([]);

    $row = $this->form->buildRow($currency);
    $expected = array(
      'id' => $entity_id,
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
