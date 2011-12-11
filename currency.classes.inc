<?php

/**
 * @file
 * Provides the Currency and CurrencyLanguage classes.
 */

/**
 * Describes a currency.
 */
class Currency {

  /**
   * ISO 3166-1 alpha 2 codes that define in which countries this currency is
   * used.
   *
   * @var array
   */
  public $country_codes = array();

  /**
   * ISO 4217 currency code.
   *
   * @var string
   */
  public $code = 'XXX';

  /**
   * The number of decimals used to represent an amount in this currency.
   *
   * @var integer
   */
  public $decimals = 2;

  /**
   * Whether the currency is locked (not user editable).
   *
   * @var boolean
   */
  public $locked = FALSE;

  /**
   * The currency's official sign, such as '€' or '$'.
   *
   * @var string
   */
  public $sign = '¤';

  /**
   * The number of subunits one unit of this currency has.
   *
   * @var integer
   */
  public $subunits = 100;

  /**
   * Human-readable title in US English.
   *
   * @var string
   */
  public $title = '';

  function __construct(array $properties = array()) {
    foreach ($properties as $property => $value) {
      $this->$property = $value;
    }
  }
}

/**
 * Describes how currencies should behave in certain languages.
 */
class CurrencyLanguage {

  /**
   * Whether to display the currency code before or after the amount.
   *
   * @var integer
   *   Either CURRENCY_INDICATE_BEFORE or CURRENCY_INDICATOR_AFTER.
   */
  public $code_position = '';

  /**
   * The decimal separation character.
   *
   * @var string
   */
  public $decimal_separator = '';

  /**
   * ISO 639-1 alpha 2 language code.
   *
   * @var string
   */
  public $language_code = '';

  /**
   * Whether to display the currency sign before or after the amount.
   *
   * @var integer
   *   Either CURRENCY_INDICATE_BEFORE or CURRENCY_INDICATOR_AFTER.
   */
  public $sign_position = '';

  /**
   * The thousands separation character.
   *
   * @var string
   */
  public $thousands_separator = '';

  function __construct(array $properties = array()) {
    foreach ($properties as $property => $value) {
      $this->$property = $value;
    }
  }
}
