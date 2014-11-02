<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Plugin\views\field\CurrencyWebTest.
 */

namespace Drupal\currency\Tests\Plugin\views\field;

use Drupal\simpletest\WebTestBase;

/**
 * \Drupal\currency\Plugin\views\field\Currency web test.
 *
 * @group Currency
 */
class CurrencyWebTest extends WebTestBase {

  public static $modules = array('currency_test', 'views');

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
   * Tests the handler.
   */
  public function testHandler() {
    /** @var \Drupal\views\Entity\View $view */
    $view = entity_load('view', 'currency_test');
    $view->getExecutable()->execute('default');
    $this->assertEqual($view->get('executable')->field['currency_sign']->advancedRender($view->get('executable')->result[0]), '€');
    $this->assertEqual($view->get('executable')->field['currency_subunits']->advancedRender($view->get('executable')->result[0]), '100');
  }
}
