<?php

/**
 * @file
 * API documentation.
 */

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
