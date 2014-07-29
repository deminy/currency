<?php

/**
 * @file
 * Definition of Drupal\currency\FormHelperInterface.
 */

namespace Drupal\currency;

/**
 * Defines form helpers.
 */
interface FormHelperInterface {

  /**
   * Returns an options list of all currencies.
   *
   * @return array
   *   Keys are currency codes. Values are human-readable currency labels.
   */
  public function getCurrencyOptions();

}
