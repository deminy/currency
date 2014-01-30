<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\ModuleInstallUninstallWebTest.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests module installation and uninstallation.
 */
class ModuleInstallUninstallWebTest extends WebTestBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Module installation and uninstallation',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->moduleHandler = $this->container->get('module_handler');
  }

  /**
   * Test uninstall.
   */
  function testUninstallation() {
    $this->assertTrue($this->moduleHandler->moduleExists('currency'));
    $this->moduleHandler->uninstall(array('currency'));
    $this->assertFalse($this->moduleHandler->moduleExists('currency'));
  }

  /**
   * Test configuration import.
   */
  function testConfigImport() {
    // The Dutch guilder was replaced by the Belgian franc, the euro, and the
    // Surinamese guilder. This means it is obsolete and should be disabled by
    // default.
    $currency = entity_load('currency', 'NLG');
    $this->assertFalse($currency->status());

    // The euro is still in use in most countries and should be enabled by
    // default.
    $currency = entity_load('currency', 'EUR');
    $this->assertTrue($currency->status());
  }
}
