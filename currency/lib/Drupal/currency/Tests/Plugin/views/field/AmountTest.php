<?php

/**
 * @file
 * Contains Drupal\currency\Tests\Plugin\views\field\AmountTest.
 */

namespace Drupal\currency\Tests\Plugin\views\field;

use Drupal\simpletest\WebTestBase;

/**
 * Tests Drupal\currency\Plugin\views\field\Amount.
 */
class AmountTest extends WebTestBase {

  public static $modules = array('currency_test', 'views_ui');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Plugin\views\field\Amount',
      'group' => 'Currency',
      'dependencies' => array('views'),
    );
  }

  /**
   * Tests the handler.
   */
  public function testHandler() {
    $view_id = 'currency_test';

    // Test view creation/editing.
    $account = $this->drupalCreateUser(array('administer views'));
    $this->drupalLogin($account);
    $this->drupalPostForm('admin/structure/views/nojs/config-item/' . $view_id . '/default/field/amount_currency_code_definition', array(
      'options[currency_round]' => TRUE,
    ), t('Apply'));
    $this->drupalPostForm('admin/structure/views/view/' . $view_id, array(), t('Save'));

    // Test view display.
    $view = entity_load('view', $view_id);
    $view->getExecutable()->execute('default');
    $values = array(
      array(
        // The amount_currency_code_definition field is rounded.
        'amount_currency_code_definition' => '€123.46',
        'amount_currency_code_field_definition' => '€123.456',
        'amount_currency_code_field_table_definition' => '€123.456',
        'amount_currency_undefined' => '¤123.456',
      ),
      array(
        // The amount_currency_code_definition field is rounded.
        'amount_currency_code_definition' => '€123.46',
        'amount_currency_code_field_definition' => '$123.456',
        'amount_currency_code_field_table_definition' => '$123.456',
        'amount_currency_undefined' => '¤123.456',
      ),
      array(
        // The amount_currency_code_definition field is rounded.
        'amount_currency_code_definition' => '€123.46',
        'amount_currency_code_field_definition' => '₴123.456',
        'amount_currency_code_field_table_definition' => '₴123.456',
        'amount_currency_undefined' => '¤123.456',
      ),
    );
    foreach ($values as $row => $row_values) {
      foreach ($row_values as $field => $value) {
        $this->assertEqual($view->get('executable')->field[$field]->advancedRender($view->get('executable')->result[$row]), $value);
      }
    }
  }
}
