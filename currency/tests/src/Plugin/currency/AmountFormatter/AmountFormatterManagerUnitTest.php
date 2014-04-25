<?php

/**
 * @file Contains
 * \Drupal\currency\Tests\Plugin\Currency\AmountFormatter\AmountFormatterManagerUnitTest.
 */

namespace Drupal\currency\Plugin\Currency\AmountFormatter;
use Drupal\Tests\UnitTestCase;
use Zend\Stdlib\ArrayObject;

/**
 * Tests \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManager.
 */
class AmountFormatterManagerUnitTest extends UnitTestCase {

  /**
   * The cache backend used for testing.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $cache;

  /**
   * The config factory used for testing.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

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
   * The plugin factory used for testing.
   *
   * @var \Drupal\Core\Language\LanguageManager|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $languageManager;

  /**
   * The module handler used for testing.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The amount formatter plugin manager under test.
   *
   * @var \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManager
   */
  public $currencyAmountFormatterManager;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManager unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $this->discovery = $this->getMock('\Drupal\Component\Plugin\Discovery\DiscoveryInterface');

    $this->factory = $this->getMockBuilder('\Drupal\Component\Plugin\Factory\DefaultFactory')
      ->disableOriginalConstructor()
      ->getMock();

    $language = (object) array(
      'id' => $this->randomName(),
    );
    $this->languageManager = $this->getMockBuilder('\Drupal\Core\Language\LanguageManager')
      ->disableOriginalConstructor()
      ->getMock();
    $this->languageManager->expects($this->any())
      ->method('getCurrentLanguage')
      ->will($this->returnValue($language));

    $this->moduleHandler = $this->getMock('\Drupal\Core\Extension\ModuleHandlerInterface');

    $this->cache = $this->getMock('\Drupal\Core\Cache\CacheBackendInterface');

    $this->configFactory = $this->getMockBuilder('\Drupal\Core\Config\ConfigFactory')
      ->disableOriginalConstructor()
      ->getMock();

    $namespaces = new ArrayObject();

    $this->currencyAmountFormatterManager = new AmountFormatterManager($namespaces, $this->cache, $this->languageManager, $this->moduleHandler, $this->configFactory);
    $discovery_property = new \ReflectionProperty($this->currencyAmountFormatterManager, 'discovery');
    $discovery_property->setAccessible(TRUE);
    $discovery_property->setValue($this->currencyAmountFormatterManager, $this->discovery);
    $factory_property = new \ReflectionProperty($this->currencyAmountFormatterManager, 'factory');
    $factory_property->setAccessible(TRUE);
    $factory_property->setValue($this->currencyAmountFormatterManager, $this->factory);
  }

  /**
   * Tests getDefaultPluginId().
   */
  public function testGetDefaultPluginId() {
    $plugin_id = $this->randomName();

    $config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->once())
      ->method('get')
      ->with('plugin_id')
      ->will($this->returnValue($plugin_id));

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('currency.amount_formatter')
      ->will($this->returnValue($config));

    $this->assertSame($plugin_id, $this->currencyAmountFormatterManager->getDefaultPluginId());
  }

  /**
   * Tests setDefaultPluginId().
   */
  public function testSetDefaultPluginId() {
    $plugin_id = $this->randomName();

    $config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->once())
      ->method('set')
      ->with('plugin_id', $plugin_id)
      ->will($this->returnSelf());
    $config->expects($this->once())
      ->method('save');

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('currency.amount_formatter')
      ->will($this->returnValue($config));

    $this->assertSame(spl_object_hash($this->currencyAmountFormatterManager), spl_object_hash($this->currencyAmountFormatterManager->setDefaultPluginId($plugin_id)));
  }

  /**
   * Tests createInstance().
   */
  public function testCreateInstance() {
    $existing_plugin_id = 'currency_basic';
    $non_existing_plugin_id = $this->randomName();

    $formatter = $this->getMock('\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface');

    $this->discovery->expects($this->at(0))
      ->method('getDefinitions')
      ->will($this->returnValue(array(
        $existing_plugin_id => array(),
      )));
    $this->factory->expects($this->exactly(2))
      ->method('createInstance')
      ->with($existing_plugin_id, array())
      ->will($this->returnValue($formatter));

    $this->assertSame(spl_object_hash($formatter), spl_object_hash($this->currencyAmountFormatterManager->createInstance($existing_plugin_id)));
    $this->assertSame(spl_object_hash($formatter), spl_object_hash($this->currencyAmountFormatterManager->createInstance($non_existing_plugin_id)));
  }

  /**
   * Tests getDefaultPlugin().
   */
  public function testGetDefaultPlugin() {
    $namespaces = new ArrayObject();

    $default_plugin_id = $this->randomName();

    $formatter = $this->getMock('\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface');

    /** @var \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManager|\PHPUnit_Framework_MockObject_MockObject $currency_amount_formatter_manager */
    $currency_amount_formatter_manager = $this->getMockBuilder('\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManager')
    ->setConstructorArgs(array($namespaces, $this->cache, $this->languageManager, $this->moduleHandler, $this->configFactory))
    ->setMethods(array('getDefaultPluginId', 'createInstance'))
    ->getMock();
    $currency_amount_formatter_manager->expects($this->once())
      ->method('getDefaultPluginId')
      ->will($this->returnValue($default_plugin_id));
    $currency_amount_formatter_manager->expects($this->once())
      ->method('createInstance')
      ->with($default_plugin_id)
      ->will($this->returnValue($formatter));

    $this->assertSame(spl_object_hash($formatter), spl_object_hash($currency_amount_formatter_manager->getDefaultPlugin()));
  }
}
