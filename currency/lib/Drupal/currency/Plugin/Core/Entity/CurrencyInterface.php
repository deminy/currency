<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyInterface.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines a currency.
 */
interface CurrencyInterface extends ConfigEntityInterface {

  /**
   * Sets alternative (non-official) currency signs.
   *
   * @param array $signs
   *   Values are currency signs.
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setAlternativeSigns(array $signs);

  /**
   * Gets alternative (non-official) currency signs.
   *
   * @return array
   *   Values are currency signs.
   */
  public function getAlternativeSigns();

  /**
   * Sets the ISO 4217 currency number.
   *
   * @param string $number
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setCurrencyNumber($number);

  /**
   * Gets the ISO 4217 currency number.
   *
   * @return string
   */
  public function getCurrencyNumber();

  /**
   * Sets exchange rates.
   *
   * @param array $rates
   *   Keys are ISO 4217 codes, values are numeric strings.
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setExchangeRates(array $rates);

  /**
   * Gets exchange rates.
   *
   * @return array
   *   Keys are ISO 4217 codes, values are numeric strings.
   */
  public function getExchangeRates();

  /**
   * Sets the label.
   *
   * @param string $label
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setLabel($label);

  /**
   * Sets the number of subunits to round amounts in this currency to.
   *
   * @param int $step
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setRoundingStep($step);

  /**
   * Gets the number of subunits to round amounts in this currency to.
   *
   * @return int|null
   */
  public function getRoundingStep();

  /**
   * Sets the currency sign.
   *
   * @param string $sign
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setSign($sign);

  /**
   * Gets the currency sign.
   *
   * @return string
   */
  public function getSign();

  /**
   * Sets the number of subunits.
   *
   * @param int $subunits
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setSubunits($subunits);

  /**
   * Gets the number of subunits. @param int $subunits
   *
   * @return int
   */
  public function getSubunits();

  /**
   * Sets the currency usage.
   *
   * @param array
   *   An array of \Drupal\currency\Usage objects.
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyInterface
   */
  public function setUsage(array $usage);

  /**
   * Gets the currency usage.
   *
   * @return array
   *   An array of \Drupal\currency\Usage objects.
   */
  public function getUsage();

  /**
   * Returns the number of decimals.
   *
   * @return int
   */
  public function getDecimals();

  /**
   * Returns an options list of all currencies.
   *
   * @return array
   *   Keys are currency codes. Values are human-readable currency labels.
   */
  public static function options();

  /**
   * Format an amount using this currency and the environment's default locale
   * pattern.
   *
   * @param string $amount
   *   A numeric string.
   *
   * @return string
   */
  function format($amount);

  /**
   * Rounds an amount.
   *
   * @param string $amount
   *   A numeric string.
   *
   * @return string
   *   A numeric string.
   */
  function roundAmount($amount);

  /**
   * Checks if the currency is no longer used in the world.
   *
   * @param int $reference
   *   A Unix timestamp to check the currency's usage for. Defaults to now.
   *
   * @return bool|null
   */
  function isObsolete($reference = NULL);
}
