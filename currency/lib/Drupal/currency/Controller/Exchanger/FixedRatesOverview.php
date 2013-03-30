<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\Exchanger\FixedRatesOverview.
 */

namespace Drupal\currency\Controller\Exchanger;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\ControllerInterface;
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
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   The config factory.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Implements \Drupal\Core\ControllerInterface::create().
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'));
  }

  /**
   * Views the configured fixed rates.
   *
   * @return array
   *   A renderable array.
   */
  public function overview() {
    $rates = $this->configFactory->get('currency.exchanger.fixed_rates')->get('rates');

    $form['rates'] = array(
      '#empty' => t('There are no exchange rates yet. <a href="@path">Add an exchange rate</a>.', array(
        '@path' => url('admin/config/regional/currency-exchange/fixed/add'),
      )),
      '#header' => array(t('From'), t('To'), t('Exchange rate'), t('Operations')),
      '#type' => 'table',
    );
    foreach ($rates as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $currency_code_to => $rate) {
        // @todo Use human-readable currency names and a formatted rate.
        $row['currency_from'] = array(
          '#markup' => $currency_code_from,
          '#title' => t('From'),
          '#title_display' => 'invisible',
          '#type' => 'item',
        );
        $row['currency_to'] = array(
          '#markup' => $currency_code_to,
          '#title' => t('To'),
          '#title_display' => 'invisible',
          '#type' => 'item',
        );
        $row['rate'] = array(
          '#markup' => $rate,
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
        $form['exchangers'][] = $row;
      }
    }

    return $form;
  }
}
