<?php

/**
 * @file
 * Contains \Drupal\currency\Usage.
 */

namespace Drupal\currency;

/**
 * Describes a currency's usage in a country.
 */
class Usage  {

  /**
   * The ISO 8601 datetime of the moment this usage started.
   *
   * @var string
   */
  public $usageFrom = NULL;

  /**
   * The ISO 8601 datetime of the moment this usage ended.
   *
   * @var string
   */
  public $usageTo = NULL;

  /**
   * An ISO 3166-1 alpha-1 country code.
   *
   * @var string
   */
  public $countryCode = NULL;

  /**
   * Constructs a new class instance.
   *
   * @param array $properties
   *   Keys are property names. Values are the property values to set.
   */
  public function __construct(array $properties = array()) {
    foreach ($properties as $property => $value) {
      $this->$property = $value;
    }
  }
}
