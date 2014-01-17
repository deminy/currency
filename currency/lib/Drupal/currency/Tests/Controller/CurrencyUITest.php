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
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Controller\CurrencyUI',
      'group' => 'Currency',
    );
  }

  /**
   * Tests listing().
   */
  function testListing() {
    $account = $this->drupalCreateUser(array('currency.currency.view', 'currency.currency.delete'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/config/regional/currency');
    $this->assertText('euro');
    $this->assertLinkByHref('admin/config/regional/currency/EUR');
    $this->assertLinkByHref('admin/config/regional/currency/EUR/delete');
    // Make sure that there is an edit link, but no delete link for the default
    // currency.
    $this->assertLinkByHref('admin/config/regional/currency/XXX');
    $this->assertNoLinkByHref('admin/config/regional/currency/XXX/delete');
  }
}
