<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\ModuleInstallUninstallWebTest.
 */

namespace Drupal\currency\Tests;

use Drupal\currency\Entity\Currency;
use Drupal\currency\Entity\CurrencyInterface;
use Drupal\currency\Entity\CurrencyLocale;
use Drupal\currency\Entity\CurrencyLocaleInterface;
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
   * Tests installation and uninstallation.
   */
  function testInstallationAndUninstallation() {
    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $module_installer */
    $module_installer = \Drupal::service('module_installer');
    $module_handler = \Drupal::moduleHandler();;

    $this->assertTrue(Currency::load('XXX') instanceof CurrencyInterface);
    $this->assertTrue(CurrencyLocale::load('en_US') instanceof CurrencyLocaleInterface);

    $module_installer->uninstall(array('currency'));
    $this->assertFalse($module_handler->moduleExists('currency'));
  }

}
