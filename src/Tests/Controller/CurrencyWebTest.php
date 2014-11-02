<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\CurrencyWebTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * \Drupal\currency\Controller\Currency web test.
 *
 * @group Currency
 */
class CurrencyWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    /** @var \Drupal\currency\ConfigImporterInterface $config_importer */
    $config_importer = \Drupal::service('currency.config_importer');
    $config_importer->importCurrency('AFN');
    $config_importer->importCurrency('EUR');
  }

  /**
   * Tests the user interface.
   */
  function testUserInterface() {
    $currency_overview_path = 'admin/config/regional/currency';
    $regional_path = 'admin/config/regional';

    // Test the appearance of the link on the "Regional and language" page.
    $account = $this->drupalCreateUser(array('access administration pages'));
    $this->drupalLogin($account);
    $this->drupalGet($regional_path);
    $this->assertResponse('200');
    $this->assertNoLinkByHref($currency_overview_path);
    $this->drupalGet($currency_overview_path);
    $this->assertResponse('403');
    $account = $this->drupalCreateUser(array('currency.currency.view', 'access administration pages'));
    $this->drupalLogin($account);
    $this->drupalGet($regional_path);
    $this->assertResponse('200');
    $this->assertLinkByHref($currency_overview_path);
    $this->drupalLogout();

    // Test the currency locale overview.
    $this->drupalGet($currency_overview_path);
    $this->assertResponse('403');
    $account = $this->drupalCreateUser(array('currency.currency.view'));
    $this->drupalLogin($account);
    $this->drupalGet($currency_overview_path);
    $this->assertResponse('200');
    $this->assertText('euro');
    $this->assertNoLink(t('Edit'));
    $this->assertNoLink(t('Delete'));
    $account = $this->drupalCreateUser(array('currency.currency.view', 'currency.currency.update', 'currency.currency.delete'));
    $this->drupalLogin($account);
    $this->drupalGet($currency_overview_path);
    $this->assertLinkByHref('admin/config/regional/currency/EUR');
    $this->assertLinkByHref('admin/config/regional/currency/EUR/delete');
    // Make sure that there is an edit link, but no delete link for the default
    // currency.
    $this->assertLinkByHref('admin/config/regional/currency/XXX');
    $this->assertNoLinkByHref('admin/config/regional/currency/XXX/delete');

    // Test that the "Edit" operation link works.
    $this->clickLink(t('Edit'));
    $this->assertUrl('admin/config/regional/currency/AFN');
    $this->assertResponse('200');
    // Test that the "Delete" form action button works.
    $this->clickLink(t('Delete'));
    $this->assertUrl('admin/config/regional/currency/AFN/delete');
    $this->assertResponse('200');

    // Test that the "Delete" operation link works.
    $this->drupalGet($currency_overview_path);
    $this->clickLink(t('Delete'));
    $this->assertUrl('admin/config/regional/currency/AFN/delete');
    $this->assertResponse('200');
  }
}
