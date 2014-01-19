<?php

/**
 * @file
 * Contains \Drupal\currency_test\CurrencySignElement.
 */

namespace Drupal\currency_test;

use Drupal\Core\Form\FormInterface;

/**
 * Provides a form to test the currency_sign element.
 */
class CurrencySignElement implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'currency_test_currency_sign_element';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $currency_code = NULL, $currency_sign = NULL) {
    // Nest the element to make sure that works.
    $form['container'] = array(
      '#tree' => TRUE,
    );
    $form['container']['sign'] = array(
      '#currency_code' => $currency_code ? $currency_code : FALSE,
      '#default_value' => $currency_sign,
      '#type' => 'currency_sign',
      '#title' => t('Foo sign'),
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    \Drupal::state()->set('currency_test_currency_sign_element', $form_state['values']['container']['sign']);
  }
}
