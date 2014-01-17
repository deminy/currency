<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\CurrencyLocalePatternWebTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Controller\CurrencyLocalePattern.
 */
class CurrencyLocalePatternWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Controller\CurrencyLocalePattern web test',
      'group' => 'Currency',
    );
  }

  /**
   * Tests listing().
   */
  function testListing() {
    $account = $this->drupalCreateUser(array('currency.currency_locale_pattern.view', 'currency.currency_locale_pattern.update', 'currency.currency_locale_pattern.delete'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/config/regional/currency_locale_pattern');
    $this->assertText('Dutch (Netherlands)');
    $this->assertLinkByHref('admin/config/regional/currency_locale_pattern/nl_NL');
    $this->assertLinkByHref('admin/config/regional/currency_locale_pattern/nl_NL/delete');
    /** @var \Drupal\currency\LocaleDelegator $locale_delegator */
    $locale_delegator = \Drupal::service('currency.locale_delegator');
    // Make sure that there is an edit link, but no delete link for the default
    // locale pattern.
    $this->assertLinkByHref('admin/config/regional/currency_locale_pattern/' . $locale_delegator::DEFAULT_LOCALE);
    $this->assertNoLinkByHref('admin/config/regional/currency_locale_pattern/' . $locale_delegator::DEFAULT_LOCALE . '/delete');
  }
}
