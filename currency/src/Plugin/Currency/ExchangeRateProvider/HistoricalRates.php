<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\currency\ExchangeRate;
use Drupal\currency\Math\MathInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides historical exchange rates.
 *
 * @CurrencyExchangeRateProvider(
 *   id = "currency_historical_rates",
 *   label = @Translation("Historical rates")
 * )
 */
class HistoricalRates extends PluginBase implements ExchangeRateProviderInterface, ContainerFactoryPluginInterface {

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $currencyStorage;

  /**
   * The math service.
   *
   * @var \Drupal\currency\Math\MathInterface
   */
  protected $math;

  /**
   * Constructs a new class instance
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $currency_storage
   *   The currency entity storage.
   * @param \Drupal\currency\Math\MathInterface
   *   The Currency math service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageInterface $currency_storage, MathInterface $math) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currencyStorage = $currency_storage;
    $this->math = $math;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static($configuration, $plugin_id, $plugin_definition, $entity_manager->getStorage('currency'), $container->get('currency.math'));
  }

  /**
   * {@inheritdoc}
   */
  public function load($currency_code_from, $currency_code_to) {
    $rate = NULL;

    /** @var \Drupal\currency\Entity\CurrencyInterface $currency_from */
    $currency_from = $this->currencyStorage->load($currency_code_from);
    if ($currency_from) {
      $rates_from = $currency_from->getHistoricalExchangeRates();
      if (isset($rates_from[$currency_code_to])) {
        $rate = $rates_from[$currency_code_to];
      }
    }

    // Conversion rates are two-way. If a reverse rate is unavailable, set it.
    if (!$rate) {
      /** @var \Drupal\currency\Entity\CurrencyInterface $currency_to */
      $currency_to = $this->currencyStorage->load($currency_code_to);
      if ($currency_to) {
        $rates_to = $currency_to->getHistoricalExchangeRates();
        if (isset($rates_to[$currency_code_from])) {
          $rate = $this->math->divide(1, $rates_to[$currency_code_from]);
        }
      }
    }

    if ($rate) {
      return new ExchangeRate($this->getPluginId(), NULL, $currency_code_from, $currency_code_to, $rate);
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $currency_codes) {
    $rates = array();
    foreach ($currency_codes as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $currency_code_to) {
        $rates[$currency_code_from][$currency_code_to] = $this->load($currency_code_from, $currency_code_to);
      }
    }

    return $rates;
  }
}
