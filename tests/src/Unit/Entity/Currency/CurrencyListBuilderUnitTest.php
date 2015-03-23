<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\Currency\CurrencyListBuilderUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\Currency;

use Drupal\currency\Entity\Currency\CurrencyListBuilder;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Entity\Currency\CurrencyListBuilder
 *
 * @group Currency
 */
class CurrencyListBuilderUnitTest extends UnitTestCase {

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
    $this->entityStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->entityType = $this->getMock('\Drupal\Core\Entity\EntityTypeInterface');

    $this->moduleHandler = $this->getMock('\Drupal\Core\Extension\ModuleHandlerInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $this->form = new CurrencyListBuilder($this->entityType, $this->entityStorage, $this->stringTranslation, $this->moduleHandler);
  }

  /**
   * @covers ::createInstance
   * @covers ::__construct
   */
  function testCreateInstance() {
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->will($this->returnValue($this->entityStorage));

    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager),
      array('module_handler', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->moduleHandler),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = CurrencyListBuilder::createInstance($container, $this->entityType);
    $this->assertInstanceOf('\Drupal\currency\Entity\Currency\CurrencyListBuilder', $form);
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

    $currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();
    $currency->expects($this->any())
      ->method('id')
      ->will($this->returnValue($entity_id));
    $currency->expects($this->any())
      ->method('label')
      ->will($this->returnValue($entity_label));

    $this->moduleHandler->expects($this->any())
      ->method('invokeAll')
      ->will($this->returnValue(array()));

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
