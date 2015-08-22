<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderDecorator.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use BartFeenstra\CurrencyExchange\ExchangeRateProviderInterface as GenericExchangeRateProviderInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Provides an exchange rate provider decorator.
 */
class ExchangeRateProviderDecorator extends PluginBase implements ExchangeRateProviderInterface {

  /**
   * The decorated exchange rate provider
   *
   * @var \BartFeenstra\CurrencyExchange\ExchangeRateProviderInterface
   */
  protected $exchangeRateProvider;

  /**
   * Constructs a new instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \BartFeenstra\CurrencyExchange\ExchangeRateProviderInterface
   *   The decorated exchange rate provider.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, GenericExchangeRateProviderInterface $exchange_rate_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->exchangeRateProvider = $exchange_rate_provider;
  }

  /**
   * {@inheritdoc}
   */
  public function load($source_currency_code, $destination_currency_code) {
    return $this->exchangeRateProvider->load($source_currency_code, $destination_currency_code);
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $currency_codes) {
    return $this->exchangeRateProvider->loadMultiple($currency_codes);
  }
}
