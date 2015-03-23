<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Element\CurrencySignUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Element;

use Drupal\currency\Element\CurrencySign;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Element\CurrencySign
 *
 * @group Payment Reference Field
 */
class CurrencySignUnitTest extends UnitTestCase {

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
   * The math provider.
   *
   * @var \Drupal\currency\Math\MathInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $math;

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
    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->input = $this->getMock('\Drupal\currency\InputInterface');

    $this->math = $this->getMock('\Drupal\currency\Math\MathInterface');

    $this->stringTranslation = $this->getStringTranslationStub();

    $configuration = [];
    $plugin_id = $this->randomMachineName();
    $plugin_definition = [];

    $this->element = new CurrencySign($configuration, $plugin_id, $plugin_definition, $this->stringTranslation, $this->currencyStorage, $this->input, $this->math);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->willReturn($this->currencyStorage);

    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
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
    $this->assertInstanceOf('\Drupal\currency\Element\CurrencySign', $form);
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
