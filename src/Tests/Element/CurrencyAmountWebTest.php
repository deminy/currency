<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Element\CurrencyAmountWebTest.
 */

namespace Drupal\currency\Tests\Element;

use Drupal\simpletest\WebTestBase;

/**
 * \Drupal\currency\Element\CurrencyAmount web test.
 *
 * @group Currency
 */
class CurrencyAmountWebTest extends WebTestBase {

  public static $modules = array('currency_test');

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    /** @var \Drupal\currency\ConfigImporterInterface $config_importer */
    $config_importer = \Drupal::service('currency.config_importer');
    $config_importer->importCurrency('EUR');
  }

  /**
   * Test validation.
   */
  function testValidation() {
    $state = \Drupal::state();
    $path = 'currency_test-form-element-currency-amount/50.00/100';

    // Test valid values.
    $values =  array(
      'container[amount][amount]' => '50,95',
      'container[amount][currency_code]' => 'EUR',
    );
    $this->drupalPostForm($path, $values, t('Submit'));
    $amount = $state->get('currency_test_currency_amount_element');
    $this->assertEqual(50.95, $amount['amount']);
    $this->assertEqual('EUR', $amount['currency_code']);

    // Test valid values with a predefined currency.
    $this->drupalGet($path . '/NLG');
    $this->assertNoFieldByXPath("//input[@name='container[amount][currency_code]']");
    $values =  array(
      'container[amount][amount]' => '50,95',
    );
    $this->drupalPostForm($path . '/NLG', $values, t('Submit'));
    $amount = $state->get('currency_test_currency_amount_element');
    $this->assertEqual(50.95, $amount['amount']);
    $this->assertEqual('NLG', $amount['currency_code']);

    // Test invalid values.
    $invalid_amounts = array(
      // Illegal characters.
      $this->randomMachineName(2),
      // Multiple decimal marks.
      '49,.95',
      // A value that is below the minimum.
      '49.95',
      // A value that exceeds the maximum.
      '999'
    );
    foreach ($invalid_amounts as $amount) {
      $values =  array(
        'container[amount][amount]' => $amount,
      );
      $this->drupalPostForm($path, $values, t('Submit'));
      $this->assertFieldByXPath("//input[@name='container[amount][amount]' and contains(@class, 'error')]");
      $this->assertNoFieldByXPath("//input[not(@name='container[amount][amount]') and contains(@class, 'error')]");
    }
  }
}
