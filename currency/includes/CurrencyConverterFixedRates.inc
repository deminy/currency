<?php

/**
 * @file
 * Contains class CurrencyConverterFixedRates.
 */

/**
 * Provides fixed conversion rates as provided by bartfeenstra/currency.
 */
class CurrencyConverterFixedRates implements CurrencyConverterInterface {

  /**
   * Implements CurrencyConverterInterface::load().
   */
  static function load($currency_code_from, $currency_code_to) {
    // Check if the requested rate is available.
    $currency_from = Currency::resourceLoad($currency_code_from);
    if ($currency_from && isset($currency_from->conversionRates[$currency_code_to])) {
      return $currency_from->conversionRates[$currency_code_to];
    }

    // Conversion rates are two-way. If a reverse rate is unavailable, set it.
    $currency_to = Currency::resourceLoad($currency_code_to);
    if ($currency_to && isset($currency_to->conversionRates[$currency_code_from])) {
      return 1 / $currency_to->conversionRates[$currency_code_from];
    }

    // There is no available conversion rate.
    return FALSE;
  }

  /**
   * Implements CurrencyConverterInterface::loadMultiple().
   */
  static function loadMultiple(array $currency_codes) {
    $rates = array();
    foreach ($currency_codes as $currency_code_from => $currency_codes_to) {
      if ($currency_from = Currency::resourceLoad($currency_code_from)) {
        foreach ($currency_codes_to as $currency_code_to) {
          // Check if the requested rate is available.
          if (isset($currency_from->conversionRates[$currency_code_to])) {
            $rates[$currency_code_from][$currency_code_to] = $currency_from->conversionRates[$currency_code_to];
            continue;
          }

          // Conversion rates are two-way. If a reverse rate is unavailable, set it.
          $currency_to = Currency::resourceLoad($currency_code_to);
          if ($currency_to && isset($currency_to->conversionRates[$currency_code_from])) {
            $rates[$currency_code_from][$currency_code_to] = 1 / $currency_to->conversionRates[$currency_code_from];
            continue;
          }

          // There is no available conversion rate.
          $rates[$currency_code_from][$currency_code_to] = FALSE;
        }
      }
    }

    return $rates;
  }

  /**
   * Implements CurrencyConverterInterface::loadAll().
   */
  static function loadAll() {
    $rates = array();

    // Check if the requested rate is available.
    foreach (Currency::resourceListAll() as $currency_code_from) {
      $currency = Currency::resourceLoad($currency_code_from);
      foreach ($currency->conversionRates as $currency_code_to => $rate) {
        $rates[$currency_code_from][$currency_code_to] = $rate;
      }
    }

    // Conversion rates are two-way. If a reverse rate is unavailable, set it.
    $rates_loop = $rates;
    foreach ($rates_loop as $currency_code_from => $currency_code_from_rates) {
      foreach ($currency_code_from_rates as $currency_code_to => $rate) {
        if (!isset($rates[$currency_code_to]) || !isset($rates[$currency_code_to][$currency_code_from])) {
          $rates[$currency_code_to][$currency_code_from] = 1 / $rate;
        }
      }
    }

    return $rates;
  }
}
