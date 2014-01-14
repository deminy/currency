<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\currency\MathInterface;
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
   * The math service.
   *
   * @var \Drupal\currency\MathInterface
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
   * @param \Drupal\currency\MathInterface
   *   The Currency math service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, MathInterface $math) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->math = $math;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('currency.math'));
  }

  /**
   * {@inheritdoc}
   */
  function load($currency_code_from, $currency_code_to) {
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency_from */
    $currency_from = entity_load('currency', $currency_code_from);
    $rates_from = $currency_from->getExchangeRates();
    if ($currency_from && isset($rates_from[$currency_code_to])) {
      return $rates_from[$currency_code_to];
    }

    // Conversion rates are two-way. If a reverse rate is unavailable, set it.
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency_to */
    $currency_to = entity_load('currency', $currency_code_to);
    $rates_to = $currency_to->getExchangeRates();
    if ($currency_to && isset($rates_to[$currency_code_from])) {
      $this->math->divide(1, $rates_to[$currency_code_from]);
    }

    // There is no available exchange rate.
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  function loadMultiple(array $currency_codes) {
    $rates = array();
    foreach ($currency_codes as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $currency_code_to) {
        $rates[$currency_code_from][$currency_code_to] = self::load($currency_code_from, $currency_code_to);
      }
    }

    return $rates;
  }
}
