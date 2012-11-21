<?php

/**
 * @file
 * Contains class Currency.
 */

/**
 * Describes a currency.
 */
class Currency extends CurrencyBaseAbstract {

  /**
   * ISO 4217 currency code.
   *
   * @var string
   */
  public $code = 'XXX';

  /**
   * For objects that live in code, the machine name of the module that
   * provides the default object.
   *
   * @var string
   */
  public $export_module = '';

  /**
   * A bitmask representation of an object's current storage.
   *
   * @var string
   */
  public $export_type = '';

  /**
   * The minor unit, or exponent (as in 10^$minor_unit) that results in the
   * number of minor units.
   *
   * @var integer
   */
  public $minor_unit = NULL;

  /**
   * ISO 4217 currency number.
   *
   * @var string
   */
  public $number = '999';

  /**
   * The currency's official sign, such as '€' or '$'.
   *
   * @var string
   */
  public $sign = '¤';

  /**
   * Human-readable title in US English.
   *
   * @var string
   */
  public $title = '';
}
