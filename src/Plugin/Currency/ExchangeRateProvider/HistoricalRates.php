<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginBase;
use Drupal\currency\ExchangeRate;
use Symfony\Component\Yaml\Yaml;

/**
 * Provides historical exchange rates.
 *
 * @CurrencyExchangeRateProvider(
 *   id = "currency_historical_rates",
 *   label = @Translation("Historical rates")
 * )
 */
class HistoricalRates extends PluginBase implements ExchangeRateProviderInterface {

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
        $rate = bcdiv(1, $exchange_rates[$currency_code_to][$currency_code_from], 6);
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
