<?php

/**
 * @file
 * Contains class Currency.
 */

/**
 * Describes a currency.
 */
class Currency extends BartFeenstra\Currency\Currency {

  /**
   * Implements Ctools' exportables "export_module" property.
   *
   * @var string
   */
  public $export_module = 'currency';

  /**
   * Implements Ctools' exportables "export_type" property.
   *
   * @var string
   */
  public $export_type = 0;

  /**
   * Implements Ctools' exportables "table" property.
   *
   * @var string
   */
  public $table = 'currency';

  /**
   * Implements Ctools' exportables "type" property.
   *
   * @var string
   */
  public $type = 'Local';

  /**
   * Implements __construct().
   */
  function __construct(array $properties = array()) {
    foreach ($properties as $property => $value) {
      $this->$property = $value;
    }
  }
}
