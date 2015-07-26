<?php

/**
 * @file Contains \Drupal\currency\ExchangeRateProviderInterface.
 */

namespace Drupal\currency;

/**
 * Describes a currency exchange rate provider.
 */
interface ExchangeRateProviderInterface {

  /**
   * Returns the exchange rate for two currencies.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   *
   * @return \Drupal\currency\ExchangeRateInterface|null
   */
  public function load($currency_code_from, $currency_code_to);

  /**
   * Returns the exchange rates for multiple currency combinations.
   *
   * @param array[] $currency_codes
   *   Keys are the ISO 4217 codes of source currencies, values are arrays that
   *   contain ISO 4217 codes of destination currencies. Example:
   *   array(
   *     'EUR' => array('NLG', 'DEM', 'XXX'),
   *   )
   *
   * @return array[]
   *   Keys are the ISO 4217 codes of source currencies, values are arrays of
   *   which the keys are ISO 4217 codes of destination currencies and values
   *   are \Drupal\currency\ExchangeRateInterface objects, or NULL for
   *   combinations of currencies for which no exchange rate could be found.
   */
  public function loadMultiple(array $currency_codes);

}
