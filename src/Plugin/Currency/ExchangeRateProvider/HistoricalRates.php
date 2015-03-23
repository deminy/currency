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
use Symfony\Component\Yaml\Yaml;

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
   * @param \Drupal\currency\Math\MathInterface
   *   The Currency math service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, MathInterface $math) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->math = $math;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('currency.math'));
  }

  /**
   * {@inheritdoc}
   */
  public function load($currency_code_from, $currency_code_to) {
    $rate = NULL;

    $filename = __DIR__ . '/../../../../currency.historical_exchange_rates.yml';
    $exchange_rates = Yaml::parse($filename);

    if (isset($exchange_rates[$currency_code_from][$currency_code_to])) {
      $rate = $exchange_rates[$currency_code_from][$currency_code_to];
    }

    // Conversion rates are two-way. If a reverse rate is unavailable, set it.
    if (!$rate) {
      if (isset($exchange_rates[$currency_code_to][$currency_code_from])) {
        $rate = $this->math->divide(1, $exchange_rates[$currency_code_to][$currency_code_from]);
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
