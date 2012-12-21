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
