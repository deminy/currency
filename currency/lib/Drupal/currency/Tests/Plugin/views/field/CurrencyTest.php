<?php

/**
 * @file
 * Contains Drupal\currency\Tests\Plugin\views\field\CurrencyTest.
 */

namespace Drupal\currency\Tests\Plugin\views\field;

use Drupal\simpletest\WebTestBase;

/**
 * Tests Drupal\currency\Plugin\views\field\Currency.
 */
class CurrencyTest extends WebTestBase {

  public static $modules = array('currency_test', 'views');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Plugin\views\field\Currency',
      'group' => 'Currency',
      'dependencies' => array('views'),
    );
  }

  /**
   * Tests the handler.
   */
  public function testHandler() {
    $view = entity_load('view', 'currency_test');
    $view->getExecutable()->execute('default');
    $this->assertEqual($view->get('executable')->field['currency_sign']->advancedRender($view->get('executable')->result[0]), '€');
    $this->assertEqual($view->get('executable')->field['currency_subunits']->advancedRender($view->get('executable')->result[0]), '100');
  }
}
