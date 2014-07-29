<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Plugin\views\filter\CurrencyWebTest.
 */

namespace Drupal\currency\Tests\Plugin\views\filter;

use Drupal\currency\Entity\Currency;
use Drupal\simpletest\WebTestBase;

/**
 * \Drupal\currency\Plugin\views\filter\Currency web test.
 *
 * @group Currency
 */
class CurrencyWebTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency_test', 'views_ui');

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
    /** @var \Drupal\currency\FormHelperInterface $form_helper */
    $form_helper = \Drupal::service('currency.form_helper');
    foreach ($form_helper->getCurrencyOptions() as $option) {
      $this->assertText($option);
    }
  }
}
