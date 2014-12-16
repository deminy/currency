<?php

/**
 * @file
 * Contains \Drupal\currency_intl\Tests\ModuleInstallUninstallWebTest.
 */

namespace Drupal\currency_intl\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Module installation and uninstallation.
 *
 * @group Currency Intl
 */
class ModuleInstallUninstallWebTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency_intl');

  /**
   * Test uninstall.
   */
  function testUninstallation() {
    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $module_installer */
    $module_installer = \Drupal::service('module_installer');
    $module_handler = \Drupal::moduleHandler();
    $this->assertTrue($module_handler->moduleExists('currency_intl'));
    $module_installer->uninstall(array('currency_intl'));
    $this->assertFalse($module_handler->moduleExists('currency_intl'));
  }
}
