<?php

/**
 * @file
 * Contains class CurrencyViewsHandlerFilter.
 */

use Drupal\currency\Plugin\Core\Entity\Currency;

/**
 * A Views filter handler for fields that contain ISO 4217 currency codes.
 */
class CurrencyViewsHandlerFilter extends views_handler_filter_in_operator {

  /**
   * Overrides parent::get_value_options().
   */
  function get_value_options() {
    $this->value_options = Currency::options();
  }
}
