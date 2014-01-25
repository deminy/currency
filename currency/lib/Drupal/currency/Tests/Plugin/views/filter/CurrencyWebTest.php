<?php

/**
 * @file
 * Contains Drupal\currency\Tests\Plugin\views\filter\CurrencyWebTest.
 */

namespace Drupal\currency\Tests\Plugin\views\filter;

use Drupal\currency\Entity\Currency;
use Drupal\simpletest\WebTestBase;

/**
 * Tests Drupal\currency\Plugin\views\filter\Currency.
 */
class CurrencyWebTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency_test', 'views_ui');

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Plugin\views\filter\Currency web test',
      'group' => 'Currency',
    );
  }

  /**
   * Tests the handler.
   */
  public function testHandler() {
    $view_id = 'currency_test';
    $view = entity_load('view', $view_id);
    $view->getExecutable()->execute('default');
    // There are four rows, and the filter excludes NLG.
    $this->assertEqual(count($view->get('executable')->result), 3);

    $account = $this->drupalCreateUser(array('administer views'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/structure/views/nojs/handler/' . $view_id . '/default/filter/currency');
    foreach (Currency::options() as $option) {
      $this->assertText($option);
    }
  }
}
