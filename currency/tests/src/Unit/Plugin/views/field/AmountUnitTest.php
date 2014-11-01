<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\views\field\AmountUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Filter {

use Drupal\Core\Form\FormState;
use Drupal\currency\Plugin\views\field\Amount;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\views\field\Amount
 *
 * @group Currency
 */
class AmountUnitTest extends UnitTestCase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * The currency storage used for testing.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The handler under test.
   *
   * @var \Drupal\currency\Plugin\views\field\Amount
   */
  protected $handler;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The plugin definiton.
   *
   * @var mixed[]
   */
  protected $pluginDefinition;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * The Views display handler.
   *
   * @param \Drupal\views\Plugin\views\display\DisplayPluginBase|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $viewsDisplayHandler;

  /**
   * The Views view executable.
   *
   * @param \Drupal\views\ViewExecutable|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $viewsViewExecutable;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $configuration = [];
    $plugin_id = $this->randomMachineName();
    $this->pluginDefinition = [
      'group' => $this->randomMachineName(),
      'title' => $this->randomMachineName(),
    ];

    $this->configFactory = $this->getMock('\Drupal\Core\Config\ConfigFactoryInterface');

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->moduleHandler = $this->getMock('Drupal\Core\Extension\ModuleHandlerInterface');

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->viewsDisplayHandler = $this->getMockBuilder('\Drupal\views\Plugin\views\display\DisplayPluginBase')
      ->disableOriginalConstructor()
      ->getMock();

    $this->viewsViewExecutable = $this->getMockBuilder('\Drupal\views\ViewExecutable')
      ->disableOriginalConstructor()
      ->getMock();
    $this->viewsViewExecutable->display_handler = $this->viewsDisplayHandler;

    $container = new ContainerBuilder();
    $container->set('config.factory', $this->configFactory);
    \Drupal::setContainer($container);

    $this->handler = new Amount($configuration, $plugin_id, $this->pluginDefinition, $this->stringTranslation, $this->moduleHandler, $this->currencyStorage);
    $this->handler->init($this->viewsViewExecutable, $this->viewsDisplayHandler);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->atLeastOnce())
      ->method('getStorage')
      ->with('currency')
      ->willReturn($this->currencyStorage);

    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = [
      [
        'entity.manager',
        ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
        $entity_manager
      ],
      [
        'module_handler',
        ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
        $this->moduleHandler
      ],
      [
        'string_translation',
        ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
        $this->stringTranslation
      ],
    ];
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $filter = Amount::create($container, [], '', $this->pluginDefinition);
    $this->assertInstanceOf('\Drupal\currency\Plugin\views\field\Amount', $filter);
  }

  /**
   * @covers ::defineOptions
   */
  function testDefineOptions() {
    foreach ($this->handler->defineOptions() as $option) {
      $this->assertInternalType('array', $option);
      $this->assertTrue(array_key_exists('default', $option) || array_key_exists('contains', $option));
    }
  }

  /**
   * @covers ::buildOptionsForm
   */
  function testBuildOptionsForm() {
    $this->viewsDisplayHandler->expects($this->atLeastOnce())
      ->method('getFieldLabels')
      ->willReturn([]);
    $this->viewsDisplayHandler->expects($this->atLeastOnce())
      ->method('getHandlers')
      ->with('argument')
      ->willReturn([]);

    $views_settings_config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $views_settings_config->expects($this->atLeastOnce())
      ->method('get')
      ->with('field_rewrite_elements')
      ->willReturn([]);

    $this->configFactory->expects($this->atLeastOnce())
      ->method('get')
      ->with('views.settings')
      ->willReturn($views_settings_config);

    $form = [];
    $form_state = new FormState();
    $this->handler->buildOptionsForm($form, $form_state);
    foreach ($form as $element) {
      $this->assertInternalType('array', $element);
    }
  }

}

}

namespace {

  if (!function_exists('t')) {
    function t() {
    }
  }
  if (!function_exists('drupal_render')) {
    function drupal_render() {
    }
  }

}
