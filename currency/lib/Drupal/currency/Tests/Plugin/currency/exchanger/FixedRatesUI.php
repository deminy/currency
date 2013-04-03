<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\currency\exchanger\FixedRatesUI.
 */

namespace Drupal\currency\Tests\Plugin\currency\exchanger;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Plugin\currency\exchanger\FixedRates.
 */
class FixedRatesUI extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Plugin\currency\exchanger\FixedRates UI',
      'group' => 'Currency',
    );
  }

  /**
   * Test CurrencyExchanger's UI.
   */
  function testCurrencyExchangerFixedRatesUI() {
    $plugin = drupal_container()->get('plugin.manager.currency.exchanger')->createInstance('currency_fixed_rates');

    $user = $this->drupalCreateUser(array('currency.exchanger_fixed_rates.administer'));
    $this->drupalLogin($user);
    $path = 'admin/config/regional/currency-exchange/fixed';

    // Test the overview.
    $this->drupalGet($path);
    $this->assertText(t('Add an exchange rate'));

    $currency_code_from = 'EUR';
    $currency_code_to = 'NLG';

    // Test adding a exchange rate.
    $rate = '3';
    $values = array(
      'currency_code_from' => $currency_code_from,
      'currency_code_to' => $currency_code_to,
      'rate[amount]' => $rate,
    );
    $this->drupalPost($path . '/add', $values, t('Save'));
    $this->assertIdentical($plugin->load($currency_code_from, $currency_code_to), $rate);

    // Test editing a exchange rate.
    $rate = '6';
    $values = array(
      'rate[amount]' => $rate,
    );
    $this->drupalPost($path . '/' . $currency_code_from . '/' . $currency_code_to, $values, t('Save'));
    drupal_static_reset('CurrencyExchangerFixedRates');
    $this->assertIdentical($plugin->load($currency_code_from, $currency_code_to), $rate);

    // Test deleting a exchange rate.
    $this->drupalPost($path . '/' . $currency_code_from . '/' . $currency_code_to, $values, t('Delete'));
    drupal_static_reset('CurrencyExchangerFixedRates');
    $this->assertFalse($plugin->load($currency_code_from, $currency_code_to));
  }
}
