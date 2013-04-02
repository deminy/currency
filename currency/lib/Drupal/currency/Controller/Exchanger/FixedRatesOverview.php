<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\Exchanger\FixedRatesOverview.
 */

namespace Drupal\currency\Controller\Exchanger;

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
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   The config factory.
   */
  public function __construct(ConfigFactory $configFactory, LocaleDelegator $localeDelegator) {
    $this->configFactory = $configFactory;
    $this->localeDelegator = $localeDelegator;
  }

  /**
   * Implements \Drupal\Core\ControllerInterface::create().
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'), $container->get('currency.locale_delegator'));
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
        $currency_from = entity_load('currency', $currency_code_from);
        $currency_to = entity_load('currency', $currency_code_to);
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
        $form['exchangers'][] = $row;
      }
    }

    return $form;
  }
}
