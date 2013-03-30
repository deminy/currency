<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Form\Exchanger\DelegatorFormTest.
 */

namespace Drupal\currency\Tests\Form\Exchanger;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Form\Exchanger\DelegatorForm.
 */
class DelegatorFormTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Form\Exchanger\DelegatorForm',
      'group' => 'Currency',
    );
  }

  /**
   * Gets the exchanger plugin.
   *
   * @return \Drupal\currency\Plugin\currency\exchanger\BartFeenstraCurrency
   */
  public function getPlugin() {
    return drupal_container()->get('plugin.manager.currency.exchanger')->createInstance('currency_delegator');
  }

  /**
   * Test CurrencyExchanger's UI.
   */
  function testCurrencyExchangerUI() {
    $plugin = $this->getPlugin();

    $user = $this->drupalCreateUser(array('currency.exchanger.Delegator.administer'));
    $this->drupalLogin($user);

    // Test the default configuration.
    $this->assertEqual(array(
      'currency_fixed_rates' => TRUE,
      'currency_bartfeenstra_currency' => TRUE,
    ), $plugin->loadConfiguration());
    // Test overridden configuration.
    $path = 'admin/config/regional/currency-exchange';
    $values = array(
      'exchangers[currency_fixed_rates][enabled]' => FALSE,
    );
    $this->drupalPost($path, $values, t('Save'));
    $this->assertEqual(array(
      'currency_fixed_rates' => FALSE,
      'currency_bartfeenstra_currency' => TRUE,
    ), $plugin->loadConfiguration());
  }
}
