<?php

/**
 * @file
 * Contains class CurrencyConverter.
 */

/**
 * A currency converter that uses other available currency converters to load
 * conversion rates.
 */
class CurrencyConverter implements CurrencyConverterInterface {

  /**
   * Returns the names of available currency converter classes, sorted by weight.
   *
   * @return array
   */
  static function loadConverters() {
    ctools_include('plugins');
    $plugins = ctools_get_plugins('currency', 'currency_converter');
    // Unlist this class.
    unset($plugins['CurrencyConverter']);
    usort($plugins, array('CurrencyConverter', 'sortConverters'));
    $classes = array();
    foreach ($plugins as $plugin) {
      $classes[] = $plugin['converter']['class'];
    }

    return $classes;
  }

  /**
   * Sorts converters by their weights.
   */
  static function sortConverters(array $plugin_a, array $plugin_b) {
    // To preserve the original order of plugins with an equal weight, pretend
    // that of two equal weights, the first is greater.
    return $plugin_a['weight'] < $plugin_b['weight'] ? -1 : 1;
  }

  /**
   * Implements CurrencyConverterInterface::load().
   */
  static function load($currency_code_from, $currency_code_to) {
    foreach (self::loadConverters() as $converter) {
      if ($rate = $converter::load($currency_code_from, $currency_code_to)) {
        return $rate;
      }
    }
    return FALSE;
  }

  /**
   * Implements CurrencyConverterInterface::loadMultiple().
   */
  static function loadMultiple(array $currency_codes) {
    $rates = array();
    foreach (self::loadConverters() as $converter) {
      foreach ($converter::loadMultiple($currency_codes) as $currency_code_from => $currency_codes_to) {
        foreach($currency_codes_to as $currency_code_to => $rate) {
          if ($rate !== FALSE) {
            $rates[$currency_code_from][$currency_code_to] = $rate;
          }
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
    foreach (self::loadConverters() as $converter) {
      foreach ($converter::loadAll() as $currency_code_from => $currency_codes_to) {
        foreach($currency_codes_to as $currency_code_to => $rate) {
          if ($rate !== FALSE) {
            $rates[$currency_code_from][$currency_code_to] = $rate;
          }
        }
      }
    }

    return $rates;
  }
}
