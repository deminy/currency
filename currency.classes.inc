<?php

/**
 * @file
 * Provides the CurrencyLanguage class.
 */

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
