<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\CurrencyUITest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Controller\CurrencyUI.
 */
class CurrencyUITest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Controller\CurrencyUI',
      'group' => 'Currency',
    );
  }

  /**
   * Tests listing().
   */
  function testListing() {
    $account = $this->drupalCreateUser(array('currency.currency.view'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/config/regional/currency');
    $this->assertText('euro');
  }
}
