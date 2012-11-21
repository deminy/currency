<?php

/**
 * @file
 * Contains class CurrencyBaseAbstract
 */

/**
 * Provides shared functionality for all classes.
 */
abstract class CurrencyBaseAbstract {

  /**
   * Implements __construct().
   */
  function __construct(array $properties = array()) {
    foreach ($properties as $property => $value) {
      $this->$property = $value;
    }
  }
}
