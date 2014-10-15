<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\Currency\CurrencyFormWebTest.
 */

namespace Drupal\currency\Tests\Entity\Currency;

use Drupal\currency\Element\CurrencySign;
use Drupal\simpletest\WebTestBase;

/**
 * \Drupal\currency\Entity\Currency\CurrencyForm web test.
 *
 * @group Currency
 */
class CurrencyFormWebTest extends WebTestBase {

  public static $modules = array('currency');

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
   * Test Currency's UI.
   */
  function testUI() {
    $user = $this->drupalCreateUser(array('currency.currency.view', 'currency.currency.create', 'currency.currency.update', 'currency.currency.delete'));
    $this->drupalLogin($user);
    $path = 'admin/config/regional/currency/add';

    // Test valid values.
    $valid_values = array(
      'currency_code' => 'ABC',
      'currency_number' => '123',
      'label' => 'foo',
      'rounding_step' => '1',
      'sign[sign]' => CurrencySign::CUSTOM_VALUE,
      'sign[sign_custom]' => 'foobar',
      'subunits' => 2,
      'status' => FALSE,
    );
    $this->drupalPostForm($path, $valid_values, t('Save'));
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = entity_load('currency', 'ABC');
    if ($this->assertTrue($currency)) {
      $this->assertFalse($currency->status());
    }

    // Test invalid values.
    $valid_values['currency_code'] = 'XYZ';
    $valid_values['currency_number'] = '000';
    $invalid_values = array(
      'currency_code' => array('ABC', 'EUR'),
      'currency_number' => array('abc', '978'),
      'rounding_step' => array('x'),
      'subunits' => array('x'),
    );
    foreach ($invalid_values as $name => $field_invalid_values) {
      foreach ($field_invalid_values as $invalid_value) {
        $values = array(
          $name => $invalid_value,
        ) + $valid_values;
        $this->drupalPostForm($path, $values, t('Save'));
        // Test that the invalid element is the only element to be flagged.
        $this->assertFieldByXPath("//input[@name='$name' and contains(@class, 'error')]");
        $this->assertNoFieldByXPath("//input[not(@name='$name') and contains(@class, 'error')]");
      }
    }

    // Edit and save an existing currency.
    $path = 'admin/config/regional/currency/ABC';
    $this->drupalPostForm($path, array(), t('Save'));
    $this->assertUrl('admin/config/regional/currency');
  }
}
