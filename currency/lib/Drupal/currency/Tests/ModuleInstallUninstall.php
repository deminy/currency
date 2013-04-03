<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\ModuleInstallUninstall.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests module installation and uninstallation.
 */
class ModuleInstallUninstall extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Module installation and uninstallation',
      'group' => 'Currency',
    );
  }

  /**
   * Test uninstall.
   */
  function testUninstallation() {
    $this->assertTrue(module_exists('currency'));
    module_disable(array('currency'));
    module_uninstall(array('currency'));
    $this->assertFalse(module_exists('currency'));
  }
}
