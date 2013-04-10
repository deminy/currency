<?php

/**
 * @file
 * Contains class \Drupal\currency_exchange_rate_db_table\Tests\ModuleInstallUninstall.
 */

namespace Drupal\currency_exchange_rate_db_table\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests module installation and uninstallation.
 */
class ModuleInstallUninstall extends WebTestBase {

  public static $modules = array('currency_exchange_rate_db_table');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Module installation and uninstallation',
      'group' => 'Currency exchange rate database table',
    );
  }

  /**
   * Test uninstall.
   */
  function testUninstallation() {
    $this->assertTrue(module_exists('currency_exchange_rate_db_table'));
    module_disable(array('currency_exchange_rate_db_table'));
    module_uninstall(array('currency_exchange_rate_db_table'));
    $this->assertFalse(module_exists('currency_exchange_rate_db_table'));
  }
}
