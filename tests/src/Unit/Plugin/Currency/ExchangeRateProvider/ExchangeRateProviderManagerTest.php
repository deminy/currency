<?php

/**
 * @file Contains
 * \Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManager;
use Drupal\Tests\UnitTestCase;
use Zend\Stdlib\ArrayObject;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManager
 *
 * @group Currency
 */
class ExchangeRateProviderManagerTest extends UnitTestCase {

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $cache;

  /**
   * The class resolver.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $classResolver;

  /**
   * The plugin discovery.
   *
   * @var \Drupal\Component\Plugin\Discovery\DiscoveryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $discovery;

  /**
   * The plugin factory.
   *
   * @var \Drupal\Component\Plugin\Factory\DefaultFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $factory;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManager
   */
  public $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->classResolver = $this->getMock(ClassResolverInterface::class);

    $this->discovery = $this->getMock(DiscoveryInterface::class);

    $this->factory = $this->getMockBuilder(DefaultFactory::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->moduleHandler = $this->getMock(ModuleHandlerInterface::class);

    $this->cache = $this->getMock(CacheBackendInterface::class);

    $namespaces = new ArrayObject();

    $this->sut = new ExchangeRateProviderManager($namespaces, $this->cache, $this->moduleHandler, $this->classResolver);
    $discovery_property = new \ReflectionProperty($this->sut, 'discovery');
    $discovery_property->setAccessible(TRUE);
    $discovery_property->setValue($this->sut, $this->discovery);
    $factory_property = new \ReflectionProperty($this->sut, 'factory');
    $factory_property->setAccessible(TRUE);
    $factory_property->setValue($this->sut, $this->factory);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $namespaces = new ArrayObject();
    $this->sut = new ExchangeRateProviderManager($namespaces, $this->cache, $this->moduleHandler, $this->classResolver);
  }

  /**
   * @covers ::getDefinitions
   */
  public function testGetDefinitions() {
    $plugin_id_a = $this->randomMachineName();
    $plugin_id_b = $this->randomMachineName();
    $plugin_id_c = $this->randomMachineName();

    $discovery_definitions = [
      $plugin_id_a => [
        'label' => $this->randomMachineName(),
      ],
      $plugin_id_b => [
        'label' => $this->randomMachineName(),
        'description' => $this->randomMachineName(),
      ],
      $plugin_id_c => [
        'label' => $this->randomMachineName(),
      ],
    ];

    $this->discovery->expects($this->atLeastOnce())
      ->method('getDefinitions')
      ->willReturn($discovery_definitions);

    $definitions = $this->sut->getDefinitions();
    foreach ([$plugin_id_a, $plugin_id_b, $plugin_id_c] as $plugin_id) {
      $this->assertArrayHasKey('description', $definitions[$plugin_id]);
    }
  }

}
