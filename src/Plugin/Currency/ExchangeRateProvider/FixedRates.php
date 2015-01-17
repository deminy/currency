<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\currency\ExchangeRate;
use Drupal\currency\Math\MathInterface;
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
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

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
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The configuration factory service.
   * @param \Drupal\currency\Math\MathInterface
   *   The Currency math service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactory $config_factory, MathInterface $math) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->math = $math;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('config.factory'), $container->get('currency.math'));
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
    $rates_data = $this->configFactory->get('currency.exchanger.fixed_rates')->get('rates');
    $rates = array();
    foreach ($rates_data as $rate_data) {
      $rates[$rate_data['currency_code_from']][$rate_data['currency_code_to']] = $rate_data['rate'];
    }

    return $rates;
  }

  /**
   * Saves an exchange rate.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   * @param string $rate
   *
   * @return $this
   */
  public function save($currency_code_from, $currency_code_to, $rate) {
    $config = $this->configFactory->getEditable('currency.exchanger.fixed_rates');
    $rates = $this->loadConfiguration();
    $rates[$currency_code_from][$currency_code_to] = $rate;
    // Massage the rates into a format that can be stored, as associative
    // arrays are not supported by the config system
    $rates_data = array();
    foreach ($rates as $currency_code_from => $currency_code_from_rates) {
      foreach ($currency_code_from_rates as $currency_code_to => $rate) {
        $rates_data[] = array(
          'currency_code_from' => $currency_code_from,
          'currency_code_to' => $currency_code_to,
          'rate' => $rate,
        );
      }
    }

    $config->set('rates', $rates_data);
    $config->save();

    return $this;
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
    $config = $this->configFactory->getEditable('currency.exchanger.fixed_rates');
    $rates = $config->get('rates');
    foreach ($rates as $i => $rate) {
      if ($rate['currency_code_from'] == $currency_code_from && $rate['currency_code_to'] == $currency_code_to) {
        unset($rates[$i]);
      }
    }
    $config->set('rates', $rates);
    $config->save();
  }
}
