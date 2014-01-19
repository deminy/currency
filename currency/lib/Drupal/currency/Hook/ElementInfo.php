<?php

/**
 * @file
 * Contains \Drupal\currency\Hook\ElementInfo.
 */

namespace Drupal\currency\Hook;

/**
 * Implements hook_element_info().
 *
 * @see currency_element_info()
 */
class ElementInfo {

  /**
   * Invokes the implementation.
   */
  public function invoke() {
    // An element to collect an amount of money and convert it to a numeric string.
    $elements['currency_amount'] = array(
      '#input' => TRUE,
      '#process' => array('currency_form_currency_amount_process'),
      '#default_value' => array(
        'amount' => NULL,
        'currency_code' => NULL,
      ),
      '#element_validate' => array('currency_form_currency_amount_validate'),
      // The minimum amount as a numeric string, or FALSE to omit.
      '#minimum_amount' => FALSE,
      // The maximum amount as a numeric string, or FALSE to omit.
      '#maximum_amount' => FALSE,
      // The ISO 4217 codes of the currencies the amount must be in. Use an empty
      // array to allow any currency.
      '#limit_currency_codes' => array(),
    );
    // An element to set a currency sign.
    $elements['currency_sign'] = array(
      '#input' => TRUE,
      '#process' => array('currency_form_currency_sign_process'),
      '#element_validate' => array('currency_form_currency_sign_validate'),
      // The ISO 4217 code of the currency which signs to suggest to the user.
      // Optional.
      '#currency_code' => FALSE,
    );

    return $elements;
  }

}
