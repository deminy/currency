<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\ModuleInstallUninstallWebTest.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Module installation and uninstallation.
 *
 * @group Currency
 */
class ModuleInstallUninstallWebTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency');

  /**
   * Test uninstall.
   */
  function testUninstallation() {
    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $module_installer */
    $module_installer = \Drupal::service('module_installer');
    $module_handler = \Drupal::moduleHandler();
    $this->assertTrue($module_handler->moduleExists('currency'));
    $module_installer->uninstall(array('currency'));
    $this->assertFalse($module_handler->moduleExists('currency'));
  }

  /**
   * Test configuration import.
   */
  function testConfigImport() {
    // XXX ("No currency") is the fallback currency and must always be available.
    $currency = entity_load('currency', 'XXX');
    $this->assertTrue((bool) $currency);
  }
}
