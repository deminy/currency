<?php

/**
 * @file
 * Contains class CurrencyConverterBartFeenstraCurrency.
 */

/**
 * Provides fixed conversion rates as provided by bartfeenstra/currency.
 */
class CurrencyConverterBartFeenstraCurrency implements CurrencyConverterInterface {

  /**
   * Implements CurrencyConverterInterface::load().
   */
  static function load($currency_code_from, $currency_code_to) {
    if (in_array($currency_code_from, BartFeenstra\Currency\Currency::resourceListAll()) && in_array($currency_code_to, BartFeenstra\Currency\Currency::resourceListAll())) {
      // Check if the requested rate is available.
      $currency_from = new Currency();
      $currency_from->resourceLoad($currency_code_from);
      if ($currency_from && isset($currency_from->conversionRates[$currency_code_to])) {
        return $currency_from->conversionRates[$currency_code_to];
      }

      // Conversion rates are two-way. If a reverse rate is unavailable, set it.
      $currency_to = new Currency();
      $currency_to->resourceLoad($currency_code_to);
      if ($currency_to && isset($currency_to->conversionRates[$currency_code_from])) {
        return 1 / $currency_to->conversionRates[$currency_code_from];
      }
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
      foreach ($currency_codes_to as $currency_code_to) {
        $rates[$currency_code_from][$currency_code_to] = self::load($currency_code_from, $currency_code_to);
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
      $currency = new Currency();
      $currency->resourceLoad($currency_code_from);
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

  /**
   * Implements CurrencyConverterInterface::operationsLinks().
   */
  static function operationsLinks() {}
}
