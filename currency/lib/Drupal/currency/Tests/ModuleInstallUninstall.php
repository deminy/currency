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
   * {@inheritdoc}
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
