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
    // The Dutch guilder was replaced by the Belgian franc, the euro, and the
    // Surinamese guilder. This means it is obsolete and should be disabled by
    // default.
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = entity_load('currency', 'NLG');
    $this->assertFalse($currency->status());

    // The euro is still in use in most countries and should be enabled by
    // default.
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = entity_load('currency', 'EUR');
    $this->assertTrue($currency->status());
  }
}
