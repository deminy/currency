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
    'minorUnit' => 2,
    'sign' => '€',
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
  $currencies['EUR']['minorUnit'] = 3;
}
