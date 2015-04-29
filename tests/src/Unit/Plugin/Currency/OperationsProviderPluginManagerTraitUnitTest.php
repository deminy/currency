<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\Currency\Status\OperationsProviderPluginManagerTraitUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Currency;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\currency\Plugin\Currency\OperationsProviderPluginManagerTrait;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\OperationsProviderPluginManagerTrait
 *
 * @group Currency
 */
class OperationsProviderPluginManagerTraitUnitTest extends UnitTestCase {

  /**
   * The class resolver.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $classResolver;

  /**
   * The trait under test.
   *
   * @var \Drupal\currency\Plugin\Currency\OperationsProviderPluginManagerTrait
   */
  public $trait;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->classResolver = $this->getMock('\Drupal\Core\DependencyInjection\ClassResolverInterface');
  }

  /**
   * @covers ::getOperationsProvider
   */
  public function testGetOperationsProvider() {
    $plugin_definitions = [
      'foo' => [
        'id' => 'foo',
        'operations_provider' => '\Drupal\Tests\currency\Unit\Plugin\Currency\OperationsProviderPluginManagerTraitUnitTestOperationsProvider',
      ],
      'bar' => [
        'id' => 'bar',
      ],
    ];

    $operations_provider = new \stdClass();

    $this->trait = new OperationsProviderPluginManagerTraitUnitTestPluginManager($this->classResolver, $plugin_definitions);

    $this->classResolver->expects($this->atLeastOnce())
      ->method('getInstanceFromDefinition')
      ->with($plugin_definitions['foo']['operations_provider'])
      ->willReturn($operations_provider);

    $this->assertSame($operations_provider, $this->trait->getOperationsProvider('foo'));
    $this->assertNull($this->trait->getOperationsProvider('bar'));
  }

}

class OperationsProviderPluginManagerTraitUnitTestPluginManager {

  use OperationsProviderPluginManagerTrait;

  /**
   * The plugin definitions.
   *
   * @var array
   */
  protected $pluginDefinitions = [];

  /**
   * Creates a new class instance.
   */
  public function __construct(ClassResolverInterface $class_resolver, array $plugin_definitions) {
    $this->classResolver = $class_resolver;
    $this->pluginDefinitions = $plugin_definitions;
  }

  /**
   * Returns a plugin definition.
   */
  protected function getDefinition($plugin_id) {
    return $this->pluginDefinitions[$plugin_id];
  }
}
