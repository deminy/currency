<?php

/**
 * @file
 * Contains Drupal\currency\ExchangeRateInterface;
 */

namespace Drupal\currency;

use BartFeenstra\CurrencyExchange\ExchangeRateInterface as GenericExchangeRateInterface;

/**
 * Defines an exchange rate.
 */
interface ExchangeRateInterface extends GenericExchangeRateInterface {

  /**
   * Gets the plugin ID of the exchange rate provider that provided this rate.
   *
   * @return string|null
   */
  public function getExchangeRateProviderId();

  /**
   * Sets the plugin ID of the exchange rate provider that provided this rate.
   *
   * @param string $id
   *
   * @return $this
   */
  public function setExchangeRateProviderId($id);

}
