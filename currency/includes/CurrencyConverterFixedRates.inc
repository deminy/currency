<?php

/**
 * @file
 * Contains class CurrencyConverterFixedRates.
 */

/**
 * Provides manually entered conversion rates.
 */
class CurrencyConverterFixedRates implements CurrencyConverterInterface {

  /**
   * Implements CurrencyConverterInterface::load().
   */
  static function load($currency_code_from, $currency_code_to) {
    $rates = self::loadAll();
    if (isset($rates[$currency_code_from]) && isset($rates[$currency_code_from][$currency_code_to])) {
      return $rates[$currency_code_from][$currency_code_to];
    }
    // Calculate the reverse on the fly, because adding it to the statically
    // cached data would require additional checks when deleting rates, to see
    // if the they are reversed from other rates or are originals.
    elseif (isset($rates[$currency_code_to]) && isset($rates[$currency_code_to][$currency_code_from])) {
      return 1 / $rates[$currency_code_to][$currency_code_from];
    }
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
   * Loads all available conversion rates.
   *
   * @return array
   *   The array structure is identical to the return value of
   *   self::loadMultiple().
   */
  static function loadAll() {
    $rates = &drupal_static(__CLASS__);

    if (is_null($rates)) {
      $rates = array();
      $result = db_select('currency_converter_fixed_rates', 'ccfr')
        ->fields('ccfr')
        ->execute();
      foreach ($result as $row) {
        $rates[$row->currency_code_from][$row->currency_code_to] = (float) $row->rate;
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
      'href' => 'admin/config/regional/currency-conversion/fixed',
    ));
  }

  /**
   * Saves a conversion rate.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   * @param int|float $rate
   *
   * @return int
   *   MergeQuery::STATUS_INSERT or MergeQuery::STATUS_UPDATE.
   */
  static function save($currency_code_from, $currency_code_to, $rate) {
    $rates = &drupal_static(__CLASS__);

    $key = array(
      'currency_code_from' => $currency_code_from,
      'currency_code_to' => $currency_code_to,
    );
    $merge_status = db_merge('currency_converter_fixed_rates')
      ->key($key)
      ->fields(array_merge($key, array(
        'rate' => $rate,
      )))
      ->execute();
    $rates[$currency_code_from][$currency_code_to] = $rate;

    return $merge_status;
  }

  /**
   * Deletes a conversion rate.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   *
   * @return NULL
   */
  static function delete($currency_code_from, $currency_code_to) {
    $rates = &drupal_static(__CLASS__);

    db_delete('currency_converter_fixed_rates')
      ->condition('currency_code_from', $currency_code_from)
      ->condition('currency_code_to', $currency_code_to)
      ->execute();
    unset($rates[$currency_code_from][$currency_code_to]);
  }
}
