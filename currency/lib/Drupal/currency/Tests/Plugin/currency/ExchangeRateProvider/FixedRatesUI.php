<?php

/**
 * @file Contains
 * \Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider\FixedRatesUI.
 */

namespace Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates.
 */
class FixedRatesUI extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates UI',
      'group' => 'Currency',
    );
  }

  /**
   * Test CurrencyExchanger's UI.
   */
  function testUI() {
    $plugin = \Drupal::service('plugin.manager.currency.exchange_rate_provider')->createInstance('currency_fixed_rates');

    $user = $this->drupalCreateUser(array('currency.exchange_rate_provider.fixed_rates.administer'));
    $this->drupalLogin($user);
    $path = 'admin/config/regional/currency-exchange/fixed';

    // Test the overview.
    $this->drupalGet($path);
    $this->assertText(t('Add an exchange rate'));

    $currency_code_from = 'EUR';
    $currency_code_to = 'UAH';

    // Test adding a exchange rate.
    $rate = '3';
    $values = array(
      'currency_code_from' => $currency_code_from,
      'currency_code_to' => $currency_code_to,
      'rate[amount]' => $rate,
    );
    $this->drupalPostForm($path . '/add', $values, t('Save'));
    $this->assertIdentical($plugin->load($currency_code_from, $currency_code_to), $rate);

    // Test editing a exchange rate.
    $rate = '6';
    $values = array(
      'rate[amount]' => $rate,
    );
    $this->drupalPostForm($path . '/' . $currency_code_from . '/' . $currency_code_to, $values, t('Save'));
    $this->assertIdentical($plugin->load($currency_code_from, $currency_code_to), $rate);

    // Test deleting a exchange rate.
    $this->drupalPostForm($path . '/' . $currency_code_from . '/' . $currency_code_to, $values, t('Delete'));
    $this->assertFalse($plugin->load($currency_code_from, $currency_code_to));
  }
}
