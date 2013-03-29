<?php

/**
 * @file
 * API documentation.
 */

/**
 * Expose currencies.
 *
 * @return array
 *   An array of Currency objects, keyed by their currency codes.
 */
function hook_currency_info() {
  $currencies['EUR'] = new Currency(array(
    'ISO4217Code' => 'EUR',
    'sign' => '€',
    'subunits' => 100,
    'title' => t('Euro'),
  ));

  return $currencies;
}

/**
 * Alter exposed currencies.
 *
 * @param array
 *   An array of Currency objects, keyed by their currency codes.
 *
 * @return NULL
 */
function hook_currency_info_alter(array $currencies) {
  // Let's pretend the euro has 1000 subunits.
  $currencies['EUR']['subunits'] = 1000;
}

/**
 * Expose CLDR number patterns for locales.
 *
 * @return array
 *   An array of CurrencyLocalePattern objects, keyed by their locales.
 */
function hook_currency_locale_pattern_info() {
  $locale_patterns['nl_NL'] = new CurrencyLocalePattern(array(
    'locale' => 'nl_NL',
    'pattern' => '¤#.##0,0#',
  ));

  return $locale_patterns;
}

/**
 * Alter exposed currency locale patterns.
 *
 * @param array
 *   An array of CurrencyLocalePattern objects, keyed by their locales.
 *
 * @return NULL
 */
function hook_currency_locale_pattern_info_alter(array $locale_patterns) {
  $locale_patterns['nl_NL']->pattern = '¤ #.##0,0#';
}
