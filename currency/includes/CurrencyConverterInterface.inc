<?php

/**
 * @file
 * Contains interface CurrencyConverterInterface.
 */

/**
 * Describes a currency converter.
 */
interface CurrencyConverterInterface {

  /**
   * Returns the conversion rate for two currencies.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   *
   * @return float|false
   *   A float if the rate could be found, FALSE if it couldn't.
   */
  static function load($currency_code_from, $currency_code_to);

  /**
   * Returns the conversion rates for multiple currency combinations.
   *
   * @param array $currency_codes
   *   Keys are the ISO 4217 codes of source currencies, values are arrays that
   *   contain ISO 4217 codes of destination currencies. Example:
   *   array(
   *     'EUR' => array('NLG', 'DEM', 'XXX'),
   *   )
   *
   * @return array
   *   Keys are the ISO 4217 codes of source currencies, values are arrays of
   *   which the keys are ISO 4217 codes of destination currencies and values
   *   are the exchange rates as floats, or FALSE for combinations of
   *   currencies for which no conversion rate could be found. Example:
   *   array(
   *     'EUR' => array(
   *       'NLG' => 2.20371,
   *       'DEM' => 1.95583,
   *       'XXX' => FALSE,
   *     ),
   *   )
   */
  static function loadMultiple(array $currency_codes);

  /**
   * Returns an array of operations links.
   *
   * @return array
   *   The structure is the same as that of the 'links' item of theme_links()'
   *   $variables argument.
   */
  static function operationsLinks();
}
