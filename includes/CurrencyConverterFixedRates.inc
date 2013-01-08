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
    $currency = Currency::resourceLoad($currency_code_from);
    if (isset($currency->conversionRates[$currency_code_to])) {
      return $currency->conversionRates[$currency_code_to];
    }
    return FALSE;
  }

  /**
   * Implements CurrencyConverterInterface::loadMultiple().
   */
  static function loadMultiple(array $currency_codes) {
    $rates = array();
    foreach ($currency_codes as $currency_code_from => $currency_codes_to) {
      if ($currency = Currency::resourceLoad($currency_code_from)) {
        foreach ($currency_codes_to as $currency_code_to) {
          $rates[$currency_code_from][$currency_code_to] = isset($currency->conversionRates[$currency_code_to]) ? $currency->conversionRates[$currency_code_to] : FALSE;
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
    foreach (Currency::resourceListAll() as $currency_code_from) {
      $currency = Currency::resourceLoad($currency_code_from);
      foreach ($currency->conversionRates as $currency_code_to => $rate) {
        $rates[$currency_code_from][$currency_code_to] = $rate;
      }
    }

    return $rates;
  }
}
