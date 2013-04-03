<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\Exchanger\FixedRatesOverview.
 */

namespace Drupal\currency\Controller\Exchanger;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\ControllerInterface;
use Drupal\currency\LocaleDelegator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the overview of fixed exchange rates.
 */
class FixedRatesOverview implements ControllerInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFacory;

  /**
   * A locale delegator.
   *
   * @var \Drupal\currency\LocaleDelegator
   */
  protected $localeDelegator;

  /**
   * A currency exchanger plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $pluginManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   The config factory.
   * @param \Drupal\currency\LocaleDelegator $localeDelegator
   *   The currency exchanger plugin manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $pluginManager
   *   The currency exchanger plugin manager.
   */
  public function __construct(ConfigFactory $configFactory, LocaleDelegator $localeDelegator, PluginManagerInterface $pluginManager) {
    $this->configFactory = $configFactory;
    $this->localeDelegator = $localeDelegator;
    $this->pluginManager = $pluginManager;
  }

  /**
   * Implements \Drupal\Core\ControllerInterface::create().
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'), $container->get('currency.locale_delegator'), $container->get('plugin.manager.currency.exchanger'));
  }

  /**
   * Views the configured fixed rates.
   *
   * @return array
   *   A renderable array.
   */
  public function overview() {
    $rates = $this->configFactory->get('currency.exchange_delegator.fixed_rates')->get();
    $plugin = $this->pluginManager->createInstance('currency_fixed_rates');
    $rates = $plugin->loadAll();

    $form['rates'] = array(
      '#empty' => t('There are no exchange rates yet. <a href="@path">Add an exchange rate</a>.', array(
        '@path' => url('admin/config/regional/currency-exchange/fixed/add'),
      )),
      '#header' => array(t('From'), t('To'), t('Exchange rate'), t('Operations')),
      '#type' => 'table',
    );
    foreach ($rates as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $currency_code_to => $rate) {
        $currency_from = entity_load('currency', $currency_code_from);
        $currency_to = entity_load('currency', $currency_code_to);
        if ($currency_from && $currency_to) {
          $row['currency_from'] = array(
            '#markup' => $currency_from->label(),
            '#title' => t('From'),
            '#title_display' => 'invisible',
            '#type' => 'item',
          );
          $row['currency_to'] = array(
            '#markup' => $currency_to->label(),
            '#title' => t('To'),
            '#title_display' => 'invisible',
            '#type' => 'item',
          );
          $row['rate'] = array(
            '#markup' => $this->localeDelegator->getLocalePattern()->format($currency_to, $rate),
            '#title' => t('Exchange rate'),
            '#title_display' => 'invisible',
            '#type' => 'item',
          );
          $row['operations'] = array(
            '#links' => array(array(
              'title' => t('edit'),
              'href' => 'admin/config/regional/currency-exchange/fixed/' . $currency_code_from . '/' . $currency_code_to,
            )),
            '#title' => t('Operations'),
            '#type' => 'operations',
          );
          $form['rates'][] = $row;
        }
      }
    }

    return $form;
  }
}
