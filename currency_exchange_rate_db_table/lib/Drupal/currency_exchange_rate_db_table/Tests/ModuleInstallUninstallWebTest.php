<?php

/**
 * @file
 * Contains \Drupal\currency_exchange_rate_db_table\Tests\ModuleInstallUninstallWebTest.
 */

namespace Drupal\currency_exchange_rate_db_table\Tests;

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
  public static $modules = array('currency_exchange_rate_db_table');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Module installation and uninstallation',
      'group' => 'Currency exchange rate database table',
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
    $this->assertTrue($this->moduleHandler->moduleExists('currency_exchange_rate_db_table'));
    $this->moduleHandler->uninstall(array('currency_exchange_rate_db_table'));
    $this->assertFalse($this->moduleHandler->moduleExists('currency_exchange_rate_db_table'));
  }
}
