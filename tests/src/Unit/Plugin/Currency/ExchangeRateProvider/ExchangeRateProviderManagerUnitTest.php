<?php

/**
 * @file Contains
 * \Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider;

use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManager;
use Drupal\Tests\UnitTestCase;
use Zend\Stdlib\ArrayObject;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManager
 *
 * @group Currency
 */
class ExchangeRateProviderManagerUnitTest extends UnitTestCase {

  /**
   * The cache backend used for testing.
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
   * The plugin discovery used for testing.
   *
   * @var \Drupal\Component\Plugin\Discovery\DiscoveryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $discovery;

  /**
   * The plugin factory used for testing.
   *
   * @var \Drupal\Component\Plugin\Factory\DefaultFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $factory;

  /**
   * The module handler used for testing.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The exchange rate provider plugin manager under test.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManager
   */
  public $currencyExchangeRateProviderManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->classResolver = $this->getMock('\Drupal\Core\DependencyInjection\ClassResolverInterface');

    $this->discovery = $this->getMock('\Drupal\Component\Plugin\Discovery\DiscoveryInterface');

    $this->factory = $this->getMockBuilder('\Drupal\Component\Plugin\Factory\DefaultFactory')
      ->disableOriginalConstructor()
      ->getMock();

    $this->moduleHandler = $this->getMock('\Drupal\Core\Extension\ModuleHandlerInterface');

    $this->cache = $this->getMock('\Drupal\Core\Cache\CacheBackendInterface');

    $namespaces = new ArrayObject();

    $this->currencyExchangeRateProviderManager = new ExchangeRateProviderManager($namespaces, $this->cache, $this->moduleHandler, $this->classResolver);
    $discovery_property = new \ReflectionProperty($this->currencyExchangeRateProviderManager, 'discovery');
    $discovery_property->setAccessible(TRUE);
    $discovery_property->setValue($this->currencyExchangeRateProviderManager, $this->discovery);
    $factory_property = new \ReflectionProperty($this->currencyExchangeRateProviderManager, 'factory');
    $factory_property->setAccessible(TRUE);
    $factory_property->setValue($this->currencyExchangeRateProviderManager, $this->factory);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $namespaces = new ArrayObject();
    $this->currencyExchangeRateProviderManager = new ExchangeRateProviderManager($namespaces, $this->cache, $this->moduleHandler, $this->classResolver);
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

    $definitions = $this->currencyExchangeRateProviderManager->getDefinitions();
    foreach ([$plugin_id_a, $plugin_id_b, $plugin_id_c] as $plugin_id) {
      $this->assertArrayHasKey('description', $definitions[$plugin_id]);
    }
  }

}
