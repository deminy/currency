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
    $this->assertTrue(\Drupal::moduleHandler()->moduleExists('currency_intl'));
    \Drupal::moduleHandler()->uninstall(array('currency_intl'));
    $this->assertFalse(\Drupal::moduleHandler()->moduleExists('currency_intl'));
  }
}
