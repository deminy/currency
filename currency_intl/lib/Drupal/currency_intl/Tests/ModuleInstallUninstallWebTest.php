<?php

/**
 * @file
 * Contains class \Drupal\currency_intl\Tests\ModuleInstallUninstallWebTest.
 */

namespace Drupal\currency_intl\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests module installation and uninstallation.
 */
class ModuleInstallUninstallWebTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency_intl');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Module installation and uninstallation web test',
      'group' => 'Currency Intl',
    );
  }

  /**
   * Test uninstall.
   */
  function testUninstallation() {
    $this->assertTrue(\Drupal::moduleHandler()->moduleExists('currency_intl'));
    \Drupal::moduleHandler()->uninstall(array('currency_intl'));
    $this->assertFalse(\Drupal::moduleHandler()->moduleExists('currency_intl'));
  }
}
