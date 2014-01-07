<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\currency\exchanger\BartFeenstraCurrency.
 */

namespace Drupal\currency\Plugin\currency\exchanger;

use Drupal\Component\Plugin\PluginBase;
use Drupal\currency\Exchanger\ExchangerInterface;

/**
 * Provides fixed exchange rates as provided by bartfeenstra/currency.
 *
 * @CurrencyExchanger(
 *   id = "currency_bartfeenstra_currency",
 *   label = @Translation("Historical rates")
 * )
 */
class BartFeenstraCurrency extends PluginBase implements ExchangerInterface {

  /**
   * {@inheritdoc}
   */
  function load($currency_code_from, $currency_code_to) {
    $currency_from = entity_load('currency', $currency_code_from);
    $rates_from = $currency_from->getExchangeRates();
    if ($currency_from && isset($rates_from[$currency_code_to])) {
      return $rates_from[$currency_code_to];
    }

    // Conversion rates are two-way. If a reverse rate is unavailable, set it.
    $currency_to = entity_load('currency', $currency_code_to);
    $rates_to = $currency_to->getExchangeRates();
    if ($currency_to && isset($rates_to[$currency_code_from])) {
      return bcdiv(1, $rates_to[$currency_code_from], CURRENCY_BCMATH_SCALE);
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
