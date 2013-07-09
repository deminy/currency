<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\ExchangeDelegatorFormTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Controller\ExchangeDelegatorForm.
 */
class ExchangeDelegatorFormTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Controller\ExchangeDelegatorForm',
      'group' => 'Currency',
    );
  }

  /**
   * Test CurrencyExchanger's UI.
   */
  function testCurrencyExchangerUI() {
    $exchange_delegator = \Drupal::service('currency.exchange_delegator');

    $user = $this->drupalCreateUser(array('currency.exchanger_delegator.administer'));
    $this->drupalLogin($user);

    // Test the default configuration.
    $this->assertEqual(array(
      'currency_fixed_rates' => TRUE,
      'currency_bartfeenstra_currency' => TRUE,
    ), $exchange_delegator->loadConfiguration());
    // Test overridden configuration.
    $path = 'admin/config/regional/currency-exchange';
    $values = array(
      'exchangers[currency_fixed_rates][enabled]' => FALSE,
    );
    $this->drupalPost($path, $values, t('Save'));
    $this->assertEqual(array(
      'currency_fixed_rates' => FALSE,
      'currency_bartfeenstra_currency' => TRUE,
    ), $exchange_delegator->loadConfiguration());
  }
}
