<?php

/**
 * @file
 * Contains \Drupal\currency\LocaleDelegatorInterface.
 */

namespace Drupal\currency;

use Drupal\currency\Entity\CurrencyLocaleInterface;

/**
 * Declares a locale delegator.
 */
interface LocaleDelegatorInterface {

  /**
   * The default locale.
   */
  const DEFAULT_LOCALE = 'en_US';

  /**
   * Sets the currency locale to use.
   *
   * @param \Drupal\currency\Entity\CurrencyLocaleInterface $currency_locale
   *
   * @return $this
   */
  function setCurrencyLocale(CurrencyLocaleInterface $currency_locale);

  /**
   * Loads the currency locale to use.
   *
   * If no currency locale was explicitly set, it will load one based on the
   * environment. If no country code is set using self::setCountryCode(), the
   * "site_default_country" system variable will be used instead. If a
   * CurrencyLocale could not be loaded using these country sources and
   * $language->language, the currency locale for en_US will be loaded. This is
   * consistent with Drupal's default language, which is US English.
   *
   * @throws \RuntimeException
   *
   * @return \Drupal\currency\Entity\CurrencyLocaleInterface
   */
  function getCurrencyLocale();

  /**
   * Resets the CurrencyLocale that was loaded based on environment
   * variables.
   *
   * @return $this
   */
  function resetCurrencyLocale();

  /**
   * Sets the currency locale's country for this request.
   *
   * @param string $country_code
   *   Any code that is also returned by country_get_list().
   *
   * @return $this
   */
  function setCountryCode($country_code);

  /**
   * Gets the currency locale's country for this request.
   *
   * @return null|string
   */
  function getCountryCode();
}