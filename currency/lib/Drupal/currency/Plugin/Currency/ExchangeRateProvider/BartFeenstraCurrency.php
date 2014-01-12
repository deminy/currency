<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\Currency\ExchangeRateProvider\BartFeenstraCurrency.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginBase;

/**
 * Provides fixed exchange rates as provided by bartfeenstra/currency.
 *
 * @CurrencyExchangeRateProvider(
 *   id = "currency_bartfeenstra_currency",
 *   label = @Translation("Historical rates")
 * )
 */
class BartFeenstraCurrency extends PluginBase implements ExchangeRateProviderInterface {

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
