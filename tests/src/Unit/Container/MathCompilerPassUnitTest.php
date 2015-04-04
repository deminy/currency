<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Container\MathCompilerPassUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Container;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\currency\Container\MathCompilerPass;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @coversDefaultClass \Drupal\currency\Container\MathCompilerPass
 */
class MathCompilerPassUnitTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Container\MathCompilerPass
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->sut = new MathCompilerPass();
  }

  /**
   * @covers ::process
   */
  public function testProcess() {
    $tag_name = 'currency.math';

    $container = new ContainerBuilder();

    $math = $this->getMock('\Drupal\currency\Math\MathInterface');

    // A math handler without an explicitly set weight, which must default to 0.
    $math_handler_id_a = strtolower($this->randomMachineName());
    $math_handler_definition_a = new Definition(get_class($math));
    $math_handler_definition_a->addTag($tag_name);
    $container->setDefinition($math_handler_id_a, $math_handler_definition_a);

    // A service that is not a math handler and is not tagged as such.
    $math_handler_id_b = strtolower($this->randomMachineName());
    $math_handler_definition_b = new Definition('\stdClass');
    $container->setDefinition($math_handler_id_b, $math_handler_definition_b);

    // A math handler with a weight that must come before any handlers without
    // an explicitly set weight.
    $math_handler_id_c = strtolower($this->randomMachineName());
    $math_handler_definition_c = new Definition(get_class($math));
    $math_handler_definition_c->addTag($tag_name, [
      'weight' => -9,
    ]);
    $container->setDefinition($math_handler_id_c, $math_handler_definition_c);

    $math_delegator_service_id = 'currency.math.environment_compatible_math_delegator';
    $math_delegator_definition = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Definition')
      ->disableOriginalConstructor()
      ->getMock();
    $container->setDefinition($math_delegator_service_id, $math_delegator_definition);

    $math_delegator_definition->expects($this->at(1))
      ->method('addMethodCall')
      ->with('addMathHandler', $this->callback(function($other) use($math_handler_id_a, $math_handler_id_b, $math_handler_id_c) {
        return (string) reset($other) === $math_handler_id_c;
      }));

    $this->sut->process($container);
  }

  /**
   * @covers ::process
   *
   * @expectedException \Exception
   */
  public function testProcessWithInvalidMathHandlers() {
    $tag_name = 'currency.math';

    $container = new ContainerBuilder();

    // A service that is not a math handler, but is tagged as such.
    $math_handler_id = strtolower($this->randomMachineName());
    $math_handler_definition = new Definition('\stdClass');
    $math_handler_definition->addTag($tag_name);
    $container->setDefinition($math_handler_id, $math_handler_definition);

    $math_delegator_service_id = 'currency.math.environment_compatible_math_delegator';
    $math_delegator_definition = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Definition')
      ->disableOriginalConstructor()
      ->getMock();
    $container->setDefinition($math_delegator_service_id, $math_delegator_definition);

    $this->sut->process($container);
  }

  // @todo Test definitions that are tagged as math handlers, but do not implement MathInterface.

}
