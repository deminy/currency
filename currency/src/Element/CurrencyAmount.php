<?php

/**
 * @file
 * Contains \Drupal\currency\Element\CurrencyAmount.
 */

namespace Drupal\currency\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\currency\Entity\Currency;

/**
 * Provides form callbacks for the currency_amount form element.
 */
class CurrencyAmount {

  /**
   * Implements form #process callback.
   */
  public static function process(array $element, FormStateInterface $form_state, array $form) {
    $currency_storage = \Drupal::entityManager()->getStorage('currency');

    // Validate element configuration.
    if ($element['#minimum_amount'] !== FALSE && !is_numeric($element['#minimum_amount'])) {
      throw new \RuntimeException('The minimum amount must be a number.');
    }
    if ($element['#maximum_amount'] !== FALSE && !is_numeric($element['#maximum_amount'])) {
      throw new \RuntimeException('The maximum amount must be a number.');
    }
    if ($element['#limit_currency_codes']
      && $element['#default_value']['currency_code']
      && !in_array($element['#default_value']['currency_code'], $element['#limit_currency_codes'])) {
      throw new \InvalidArgumentException('The default currency is not in the list of allowed currencies.');
    }

    // Load the default currency.
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = NULL;
    if ($element['#default_value']['currency_code']) {
      $currency = $currency_storage->load($element['#default_value']['currency_code']);
    }
    if(!$currency) {
      $currency = $currency_storage->load('XXX');
    }

    // Modify the element.
    $element['#tree'] = TRUE;
    $element['#theme_wrappers'][] = 'form_element';
    $element['#attached']['css'] = array(
      drupal_get_path('module', 'currency') . '/currency.css',
    );

    // Add the currency element.
    /** @var \Drupal\currency\FormHelperInterface $form_helper */
    $form_helper = \Drupal::service('currency.form_helper');
    if (count($element['#limit_currency_codes']) == 1) {
      $element['currency_code'] = array(
        '#value' => reset($element['#limit_currency_codes']),
        '#type' => 'value',
      );
    }
    else {
      $element['currency_code'] = array(
        '#default_value' => $currency->id(),
        '#type' => 'select',
        '#title' => t('Currency'),
        '#title_display' => 'invisible',
        '#options' => $element['#limit_currency_codes'] ? array_intersect_key($form_helper->getCurrencyOptions(), $element['#limit_currency_codes']) : $form_helper->getCurrencyOptions(),
        '#required' => $element['#required'],
      );
    }

    // Add the amount element.
    $description = NULL;
    if ($element['#minimum_amount'] !== FALSE) {
      $description = t('The minimum amount is !amount.', array(
        '!amount' => $currency->formatAmount($element['#minimum_amount']),
      ));
    }
    $element['amount'] = array(
      '#default_value' => $element['#default_value']['amount'],
      '#type' => 'textfield',
      '#title' => t('Amount'),
      '#title_display' => 'invisible',
      '#description' => $description,
      '#prefix' => count($element['#limit_currency_codes']) == 1 ? $currency->getSign() : NULL,
      '#required' => $element['#required'],
      '#size' => 9,
    );

    return $element;
  }

  /**
   * Implements form #element_validate callback.
   */
  public static function elementValidate($element, FormStateInterface $form_state, $form) {
    /** @var \Drupal\currency\Input $input */
    $input = \Drupal::service('currency.input');
    /** @var \Drupal\currency\Math\MathInterface $math */
    $math = \Drupal::service('currency.math');

    $values = $form_state->getValues();
    $value = NestedArray::getValue($values, $element['#parents']);
    $amount = $value['amount'];
    $currency_code = $value['currency_code'];

    // Confirm that the amount is numeric.
    $amount = $input->parseAmount($amount);
    if ($amount === FALSE) {
      \Drupal::formBuilder()->setError($element['amount'], $form_state, t('%title is not numeric.', array(
        '%title' => $element['#title'],
      )));
    }

    // Confirm the amount lies within the allowed range.
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = entity_load('currency', $currency_code);
    if ($element['#minimum_amount'] !== FALSE && $math->compare($element['#minimum_amount'], $amount) == 1) {
      \Drupal::formBuilder()->setError($element['amount'], $form_state, t('The minimum amount is !amount.', array(
        '!amount' => $currency->formatAmount($element['#minimum_amount']),
      )));
    }
    elseif ($element['#maximum_amount'] !== FALSE && $math->compare($amount, $element['#maximum_amount']) == 1) {
      \Drupal::formBuilder()->setError($element['amount'], $form_state, t('The maximum amount is !amount.', array(
        '!amount' => $currency->formatAmount($element['#maximum_amount']),
      )));
    }

    // The amount in $form_state is a human-readable, optionally localized
    // string, which cannot be used by other code. $amount is a numeric string
    // after running it through \Drupal::service('currency.input')->parseAmount().
    \Drupal::formBuilder()->setValue($element, array(
      'amount' => $amount,
      'currency_code' => $currency_code,
    ), $form_state);
  }

}
