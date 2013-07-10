<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternInterface.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines a currency locale pattern.
 */
interface CurrencyLocalePatternInterface extends ConfigEntityInterface {

  /**
   * Sets the decimal separator.
   *
   * @param string $separator
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternInterface
   */
  public function setDecimalSeparator($separator);

  /**
   * Gets the decimal separator.
   *
   * @return string
   */
  public function getDecimalSeparator();

  /**
   * Sets the grouping separator.
   *
   * @param string $separator
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternInterface
   */
  public function setGroupingSeparator($separator);

  /**
   * Gets the grouping separator.
   *
   * @return string
   */
  public function getGroupingSeparator();

  /**
   * {@inheritdoc}
   *
   * The ID must be the locale, which is a lower case language code, an
   * underscore and an uppercase language code.
   *
   * @return string
   */
  public function id();

  /**
   * Sets the locale.
   *
   * @see self::id()
   *
   * @param string $language_code
   * @param string $country_code
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternInterface
   */
  public function setLocale($language_code, $country_code);

  /**
   * Gets the language code.
   *
   * @return string
   */
  public function getLanguageCode();

  /**
   * Gets the country code.
   *
   * @return string
   */
  public function getCountryCode();

  /**
   * Sets the CLDR pattern.
   *
   * @param string $pattern
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternInterface
   */
  public function setPattern($pattern);

  /**
   * Gets the CLDR pattern
   *
   * @return string
   */
  public function getPattern();

  /**
   * Formats an amount.
   *
   * @param \Drupal\currency\Plugin\Core\Entity\CurrencyInterface $currency
   *   The currency the amount is in.
   * @param string $amount
   *   A numeric string.
   */
  public function format(CurrencyInterface $currency, $amount);
}
