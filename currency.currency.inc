<?php

/**
 * @file
 * Currency hook implementations.
 */

/**
 * Implements hook_currency_info().
 */
function currency_currency_info() {
  return Currency::resourceLoadAll();
}

/**
 * Implements hook_locale_pattern_info().
 */
function currency_currency_locale_pattern_info() {
  $locale_patterns = array(
    new CurrencyLocalePattern(array(
      'locale' => 'de_DE',
      'pattern' => 'XXX #,##0.00;XXX -#,##0.00',
      'symbol_decimal_separator' => ',',
      'symbol_grouping_separator' => '.',
    )),
    new CurrencyLocalePattern(array(
      'locale' => 'fr_FR',
      'pattern' => 'XXX #,##0.00;XXX -#,##0.00',
      'symbol_decimal_separator' => ',',
      'symbol_grouping_separator' => '.',
    )),
    new CurrencyLocalePattern(array(
      'locale' => 'nl_BE',
      'pattern' => '¤#,##0.00;¤-#,##0.00',
      'symbol_decimal_separator' => ',',
      'symbol_grouping_separator' => '.',
    )),
    new CurrencyLocalePattern(array(
      'locale' => 'nl_NL',
      'pattern' => '¤#,##0.00;¤-#,##0.00',
      'symbol_decimal_separator' => ',',
      'symbol_grouping_separator' => '.',
    )),
    new CurrencyLocalePattern(array(
      'locale' => 'en_UK',
      'pattern' => '¤#,##0.00;¤-#,##0.00',
    )),
    new CurrencyLocalePattern(array(
      'locale' => 'en_US',
      'pattern' => '¤#,##0.00;¤-#,##0.00',
    )),
  );

  // Key patterns by their locales, which act as their machine names.
  $locale_patterns_keyed = array();
  foreach ($locale_patterns as $locale_pattern) {
    $locale_patterns_keyed[$locale_pattern->locale] = $locale_pattern;
  }

  return $locale_patterns_keyed;
}
