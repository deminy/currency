<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\CurrencyLocalePatternUITest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Controller\CurrencyLocalePatternUI.
 */
class CurrencyLocalePatternUITest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Controller\CurrencyLocalePatternUI',
      'group' => 'Currency',
    );
  }

  /**
   * Tests listing().
   */
  function testListing() {
    $account = $this->drupalCreateUser(array('currency.currency_locale_pattern.view'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/config/regional/currency_locale_pattern');
    $this->assertText('Dutch (Netherlands)');
  }
}
