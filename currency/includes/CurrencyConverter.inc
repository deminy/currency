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
   * Loads the configuration.
   *
   * @return array
   *   Keys are currency_converter plugin names. Values are booleans that
   *   describe whether the plugins are enabled. Items are ordered by weight.
   */
  public static function loadConfiguration() {
    $configuration = variable_get('currency_converter', array());
    if (empty($configuration)) {
      ctools_include('plugins');
      $plugins = ctools_get_plugins('currency', 'currency_converter');
      $configuration = array_fill_keys(array_keys($plugins), TRUE);
    }
    // Skip CurrencyConverter, because it is a utility/service and not a
    // regular plugin. It should never be part of the configuration anyway.
    // This unset() is just a fail-safe.
    unset($configuration['CurrencyConverter']);

    return $configuration;
  }

  /**
   * Saves the configuration.
   *
   * @param array $configuration
   *   Keys are currency_converter plugin names. Values are booleans that
   *   describe whether the plugins are enabled. Items are ordered by weight.
   *
   * @return NULL
   */
  public static function saveConfiguration(array $configuration) {
    variable_set('currency_converter', $configuration);
  }

  /**
   * Returns the names of enabled currency converter classes, sorted by weight.
   *
   * @return array
   */
  static function loadConverters() {
    ctools_include('plugins');
    $plugins = ctools_get_plugins('currency', 'currency_converter');

    $names = array_keys(array_filter(self::loadConfiguration()));
    $classes = array();
    foreach ($names as $name) {
      $classes[] = $plugins[$name]['converter']['class'];
    }

    return $classes;
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
        foreach ($currency_codes_to as $currency_code_to => $rate) {
          $rates[$currency_code_from][$currency_code_to] = $rate;
          // If we found a rate, prevent it from being looked up by the next converter.
          if ($rate) {
            $index = array_search($currency_code_to, $currency_codes[$currency_code_from]);
            unset($currency_codes[$currency_code_from][$index]);
          }
        }
      }
    }

    return $rates;
  }

  /**
   * Implements CurrencyConverterInterface::operationsLinks().
   */
  static function operationsLinks() {
    return array(array(
      'title' => t('configure'),
      'href' => 'admin/config/regional/currency-conversion',
    ));
  }
}
