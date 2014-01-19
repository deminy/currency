<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\currency\ExchangeRate;
use Drupal\currency\MathInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides manually entered exchange rates.
 *
 * @CurrencyExchangeRateProvider(
 *   id = "currency_fixed_rates",
 *   label = @Translation("Fixed rates"),
 *   operations = {
 *     "admin/config/regional/currency-exchange/fixed" = @Translation("configure"),
 *   }
 * )
 */
class FixedRates extends PluginBase implements ExchangeRateProviderInterface, ContainerFactoryPluginInterface {

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
  public function load($currency_code_from, $currency_code_to) {
    $rate = NULL;

    $rates = $this->loadConfiguration();
    if (isset($rates[$currency_code_from]) && isset($rates[$currency_code_from][$currency_code_to])) {
      $rate = $rates[$currency_code_from][$currency_code_to];
    }
    // Calculate the reverse on the fly, because adding it to the statically
    // cached data would require additional checks when deleting rates, to see
    // if the they are reversed from other rates or are originals.
    elseif(isset($rates[$currency_code_to]) && isset($rates[$currency_code_to][$currency_code_from])) {
      $rate = $this->math->divide(1, $rates[$currency_code_to][$currency_code_from]);
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

  /**
   * Loads all stored exchange rates.
   *
   * @return array[]
   *   Keys are source currency codes. Values are arrays of which the keys are
   *   destination currency codes and values are exchange rates.
   */
  public function loadConfiguration() {
    $config = \Drupal::config('currency.exchanger.fixed_rates');

    return $config->get('rates');
  }

  /**
   * Saves a nexchange rate.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   * @param string $rate
   *
   * @return NULL
   */
  public function save($currency_code_from, $currency_code_to, $rate) {
    $config = \Drupal::config('currency.exchanger.fixed_rates');
    $rates = $config->get('rates');
    $rates[$currency_code_from][$currency_code_to] = $rate;
    $config->set('rates', $rates);
    $config->save();
  }

  /**
   * Deletes an exchange rate.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   *
   * @return NULL
   */
  public function delete($currency_code_from, $currency_code_to) {
    $config = \Drupal::config('currency.exchanger.fixed_rates');
    $rates = $config->get('rates');
    unset($rates[$currency_code_from][$currency_code_to]);
    $config->set('rates', $rates);
    $config->save();
  }
}
