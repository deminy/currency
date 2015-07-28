<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\views\field\CurrencyTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\views\filter;

use Drupal\currency\FormHelperInterface;
use Drupal\currency\Plugin\views\filter\Currency;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\views\filter\Currency
 *
 * @group Currency
 */
class CurrencyTest extends UnitTestCase {

  /**
   * The form helper
   *
   * @var \Drupal\currency\FormHelperInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $formHelper;

  /**
   * The handler under test.
   *
   * @var \Drupal\currency\Plugin\views\field\Currency
   */
  protected $handler;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $configuration = [];
    $plugin_id = $this->randomMachineName();
    $plugin_definition = [];

    $this->formHelper = $this->getMock(FormHelperInterface::class);

    $this->handler = new Currency($configuration, $plugin_id, $plugin_definition, $this->formHelper);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock(ContainerInterface::class);
    $map = [
      ['currency.form_helper', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->formHelper],
    ];
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $configuration = [];
    $plugin_id = $this->randomMachineName();
    $plugin_definition = [];

    $filter = Currency::create($container, $configuration, $plugin_id, $plugin_definition);
    $this->assertInstanceOf(Currency::class, $filter);
  }

  /**
   * @covers ::getValueOptions
   */
  public function testGetValueOptions() {
    $options = array(
      $this->randomMachineName() => $this->randomMachineName(),
    );

    $this->formHelper->expects($this->atLeastOnce())
      ->method('getCurrencyOptions')
      ->willReturn($options);

    $method = new \ReflectionMethod($this->handler, 'getValueOptions');
    $method->setAccessible(TRUE);

    $this->assertSame($options, $method->invoke($this->handler));
  }

}
