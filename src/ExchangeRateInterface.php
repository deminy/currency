<?php

/**
 * @file Contains Drupal\currency\ExchangeRateInterface;
 */

namespace Drupal\currency;

/**
 * Defines an exchange rate.
 */
interface ExchangeRateInterface {

  /**
   * Gets the code of the destination currency.
   *
   * @return string
   *   An ISO 4217 code.
   */
  public function getDestinationCurrencyCode();

  /**
   * Sets the code of the destination currency.
   *
   * @param string $currency_code
   *   An ISO 4217 code.
   *
   * @return $this
   */
  public function setDestinationCurrencyCode($currency_code);

  /**
   * Gets the code of the source currency.
   *
   * @return string
   *   An ISO 4217 code.
   */
  public function getSourceCurrencyCode();

  /**
   * Sets the code of the source currency.
   *
   * @param string $currency_code
   *   An ISO 4217 code.
   *
   * @return $this
   */
  public function setSourceCurrencyCode($currency_code);

  /**
   * Gets the exchange rate.
   *
   * @return string
   */
  public function getRate();

  /**
   * Sets the exchange rate.
   *
   * @param string $rate
   *
   * @return $this
   */
  public function setRate($rate);

  /**
   * Gets the plugin ID of the exchange rate provider that provided this rate.
   *
   * @return string|null
   */
  public function getExchangeRateProviderPluginId();

  /**
   * Sets the plugin ID of the exchange rate provider that provided this rate.
   *
   * @param string $plugin_id
   *
   * @return $this
   */
  public function setExchangeRateProviderPluginId($plugin_id);

  /**
   * Gets the timestamp of the moment this rate was obtained.
   *
   * @return int|null
   *   A Unix timestamp.
   */
  public function getTimestamp();

  /**
   * Sets the timestamp of the moment this rate was obtained.
   *
   * @param int $timestamp
   *   A Unix timestamp.
   *
   * @return $this
   */
  public function setTimestamp($timestamp);

}
