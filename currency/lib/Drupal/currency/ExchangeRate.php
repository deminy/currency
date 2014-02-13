<?php

/**
 * @file Contains Drupal\currency\ExchangeRate;
 */

namespace Drupal\currency;

/**
 * Provides an exchange rate.
 */
class ExchangeRate implements ExchangeRateInterface {

  /**
   * The ID of the exchanger plugin that provided this rate.
   *
   * @var string
   */
  protected $exchangeRateProviderPluginId;

  /**
   * The timestamp of the moment this rate was obtained.
   *
   * @var int
   */
  protected $timestamp;

  /**
   * The code of the destination currency.
   *
   * @var string
   */
  protected $destinationCurrencyCode;

  /**
   * The code of the source currency.
   *
   * @var string
   */
  protected $sourceCurrencyCode;

  /**
   * The exchange rate.
   *
   * @var string
   */
  protected $rate;

  /**
   * Constructs a new class instance.
   *
   * @param string $exchange_rate_provider_plugin_id
   *   The ID of the exchange rate provider plugin that provided this rate.
   * @param int $timestamp
   *   The timestamp of the moment this rate was obtained.
   * @param string $source_currency_code
   *   The code of the source currency.
   * @param string $destination_currency_code
   *   The code of the destination currency.
   * @param string $rate
   *   The exchange rate.
   */
  public function __construct($exchange_rate_provider_plugin_id, $timestamp, $source_currency_code, $destination_currency_code, $rate) {
    $this->destinationCurrencyCode = $destination_currency_code;
    $this->exchangeRateProviderPluginId = $exchange_rate_provider_plugin_id;
    $this->rate = $rate;
    $this->sourceCurrencyCode = $source_currency_code;
    $this->timestamp = $timestamp;
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationCurrencyCode() {
    return $this->destinationCurrencyCode;
  }

  /**
   * {@inheritdoc}
   */
  public function setDestinationCurrencyCode($currency_code) {
    $this->destinationCurrencyCode = $currency_code;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceCurrencyCode() {
    return $this->sourceCurrencyCode;
  }

  /**
   * {@inheritdoc}
   */
  public function setSourceCurrencyCode($currency_code) {
    $this->sourceCurrencyCode = $currency_code;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRate() {
    return $this->rate;
  }

  /**
   * {@inheritdoc}
   */
  public function setRate($rate) {
    $this->rate = $rate;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getExchangeRateProviderPluginId() {
    return $this->exchangeRateProviderPluginId;
  }

  /**
   * {@inheritdoc}
   */
  public function setExchangeRateProviderPluginId($plugin_id) {
    $this->exchangeRateProviderPluginId = $plugin_id;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTimestamp() {
    return $this->timestamp;
  }

  /**
   * {@inheritdoc}
   */
  public function setTimestamp($timestamp) {
    $this->timestamp = $timestamp;

    return $this;
  }

}
