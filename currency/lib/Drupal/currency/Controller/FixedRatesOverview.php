<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\FixedRatesOverview.
 */

namespace Drupal\currency\Controller\Exchanger;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\currency\LocaleDelegatorInterface;
use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the overview of fixed exchange rates.
 */
class FixedRatesOverview extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFacory;

  /**
   * The locale delegator.
   *
   * @var \Drupal\currency\LocaleDelegatorInterface
   */
  protected $localeDelegator;

  /**
   * The currency exchange rate provider manager.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface
   */
  protected $currencyExchangeRateProviderManager;

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   * @param \Drupal\currency\LocaleDelegatorInterface $locale_delegator
   *   The currency locale delegator.
   * @param \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface $currency_exchange_rate_provider_manager
   *   The currency exchanger plugin manager.
   */
  public function __construct(ConfigFactory $config_factory, UrlGeneratorInterface $url_generator, LocaleDelegatorInterface $locale_delegator, ExchangeRateProviderManagerInterface $currency_exchange_rate_provider_manager) {
    $this->configFactory = $config_factory;
    $this->localeDelegator = $locale_delegator;
    $this->currencyExchangeRateProviderManager = $currency_exchange_rate_provider_manager;
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'), $container->get('url_generator'), $container->get('currency.locale_delegator'), $container->get('plugin.manager.currency.exchange_rate_provider'));
  }

  /**
   * Views the configured fixed rates.
   *
   * @return array
   *   A renderable array.
   */
  public function overview() {
    /** @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates $plugin */
    $plugin = $this->currencyExchangeRateProviderManager->createInstance('currency_fixed_rates');
    $rates = $plugin->loadConfiguration();

    $form['rates'] = array(
      '#empty' => $this->t('There are no exchange rates yet. <a href="@path">Add an exchange rate</a>.', array(
        '@path' => url('admin/config/regional/currency-exchange/fixed/add'),
      )),
      '#header' => array($this->t('From'), $this->t('To'), $this->t('Exchange rate'), $this->t('Operations')),
      '#type' => 'table',
    );
    foreach ($rates as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $currency_code_to => $rate) {
        $currency_from = entity_load('currency', $currency_code_from);
        $currency_to = entity_load('currency', $currency_code_to);
        if ($currency_from && $currency_to) {
          $row['currency_from'] = array(
            '#markup' => $currency_from->label(),
            '#title' => $this->t('From'),
            '#title_display' => 'invisible',
            '#type' => 'item',
          );
          $row['currency_to'] = array(
            '#markup' => $currency_to->label(),
            '#title' => $this->t('To'),
            '#title_display' => 'invisible',
            '#type' => 'item',
          );
          $row['rate'] = array(
            '#markup' => $this->localeDelegator->getCurrencyLocale()->formatAmount($currency_to, $rate),
            '#title' => $this->t('Exchange rate'),
            '#title_display' => 'invisible',
            '#type' => 'item',
          );
          $row['operations'] = array(
            '#links' => array(array(
              'title' => $this->t('edit'),
              'href' => 'admin/config/regional/currency-exchange/fixed/' . $currency_code_from . '/' . $currency_code_to,
            )),
            '#title' => $this->t('Operations'),
            '#type' => 'operations',
          );
          $form['rates'][] = $row;
        }
      }
    }

    return $form;
  }
}
