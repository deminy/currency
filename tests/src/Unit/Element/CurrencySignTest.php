<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Element\CurrencySignTest.
 */

namespace Drupal\Tests\currency\Unit\Element;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\currency\Element\CurrencySign;
use Drupal\currency\InputInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Element\CurrencySign
 *
 * @group Currency
 */
class CurrencySignTest extends UnitTestCase {

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The element under test.
   *
   * @var \Drupal\currency\Element\CurrencySign
   */
  protected $element;

  /**
   * The input parser.
   *
   * @var \Drupal\currency\InputInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $input;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->currencyStorage = $this->getMock(EntityStorageInterface::class);

    $this->input = $this->getMock(InputInterface::class);

    $this->stringTranslation = $this->getStringTranslationStub();

    $configuration = [];
    $plugin_id = $this->randomMachineName();
    $plugin_definition = [];

    $this->element = new CurrencySign($configuration, $plugin_id, $plugin_definition, $this->stringTranslation, $this->currencyStorage, $this->input);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $entity_manager = $this->getMock(EntityManagerInterface::class);
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->willReturn($this->currencyStorage);

    $container = $this->getMock(ContainerInterface::class);
    $map = array(
      array(
        'entity.manager',
        ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
        $entity_manager
      ),
      array(
        'string_translation',
        ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
        $this->stringTranslation
      ),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array();

    $form = CurrencySign::create($container, $configuration, $plugin_id, $plugin_definition);
    $this->assertInstanceOf(CurrencySign::class, $form);
  }

  /**
   * @covers ::getInfo
   */
  public function testGetInfo() {
    $info = $this->element->getInfo();
    $this->assertInternalType('array', $info);
    foreach ($info['#element_validate'] as $callback) {
      $this->assertTrue(is_callable($callback));
    }
    foreach ($info['#process'] as $callback) {
      $this->assertTrue(is_callable($callback));
    }
  }

}
