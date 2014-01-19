<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\CurrencyLocaleWebTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Controller\CurrencyLocale.
 */
class CurrencyLocaleWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Controller\CurrencyLocale web test',
      'group' => 'Currency',
    );
  }

  /**
   * Tests listing().
   */
  function testListing() {
    $account = $this->drupalCreateUser(array('currency.currency_locale.view', 'currency.currency_locale.update', 'currency.currency_locale.delete'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/config/regional/currency-localization/locale');
    $this->assertText('Dutch (Netherlands)');
    $this->assertLinkByHref('admin/config/regional/currency-localization/locale/nl_NL');
    $this->assertLinkByHref('admin/config/regional/currency-localization/locale/nl_NL/delete');
    /** @var \Drupal\currency\LocaleDelegator $locale_delegator */
    $locale_delegator = \Drupal::service('currency.locale_delegator');
    // Make sure that there is an edit link, but no delete link for the default
    // currency locale.
    $this->assertLinkByHref('admin/config/regional/currency-localization/locale/' . $locale_delegator::DEFAULT_LOCALE);
    $this->assertNoLinkByHref('admin/config/regional/currency-localization/locale/' . $locale_delegator::DEFAULT_LOCALE . '/delete');
  }
}
