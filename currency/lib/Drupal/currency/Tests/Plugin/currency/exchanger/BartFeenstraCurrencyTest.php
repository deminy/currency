<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\currency\exchanger\BartFeenstraCurrencyTest.
 */

namespace Drupal\currency\Tests\Plugin\currency\exchanger;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Plugin\currency\exchanger\BartFeenstraCurrency.
 */
class BartFeenstraCurrencyTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Overrides parent::getInfo().
   */
  public static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Plugin\currency\exchanger\BartFeenstraCurrency',
      'group' => 'Currency',
    );
  }

  /**
   * Gets the exchanger plugin.
   *
   * @return \Drupal\currency\Plugin\currency\exchanger\BartFeenstraCurrency
   */
  public function getPlugin() {
    return drupal_container()->get('plugin.manager.currency.exchanger')->createInstance('currency_bartfeenstra_currency');
  }

  /**
   * Tests load().
   */
  function testLoad() {
    $plugin = $this->getPlugin();

    // Test an available exchange rate.
    $this->assertIdentical($plugin->load('EUR', 'NLG'), '2.20371');

    // Test an unavailable exchange rate for which the reverse rate is
    // available.
    $this->assertIdentical($plugin->load('NLG', 'EUR'), '0.453780216');
  }

  /**
   * Tests loadMultiple().
   */
  function testLoadMultiple() {
    $plugin = $this->getPlugin();

    // Test an available exchange rate.
    $rates = $plugin->loadMultiple(array(
      'EUR' => array('NLG'),
    ));
    $this->assertTrue(isset($rates['EUR']));
    $this->assertTrue(isset($rates['EUR']['NLG']));
    $this->assertIdentical($rates['EUR']['NLG'], '2.20371');

    // Test an unavailable exchange rate for which the reverse rate is
    // available.
    $rates = $plugin->loadMultiple(array(
      'NLG' => array('EUR'),
    ));
    $this->assertTrue(isset($rates['NLG']));
    $this->assertTrue(isset($rates['NLG']['EUR']));
    $this->assertIdentical($rates['NLG']['EUR'], '0.453780216');
  }
}