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
    $this->assertTrue(\Drupal::moduleHandler()->moduleExists('currency'));
    \Drupal::moduleHandler()->uninstall(array('currency'));
    $this->assertFalse(\Drupal::moduleHandler()->moduleExists('currency'));
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
