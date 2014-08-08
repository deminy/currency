<?php

/**
 * @file
 * Contains \Drupal\currency\Element\CurrencySign.
 */

namespace Drupal\currency\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides form callbacks for the currency_sign form element.
 */
class CurrencySign {

  /**
   * The value for the currency_sign form element's "custom" option.
   */
  const CUSTOM_VALUE = '###CUSTOM###';

  /**
   * Implements form #process callback.
   */
  public static function process(array $element, FormStateInterface $form_state, array $form) {
    $currency_storage = \Drupal::entityManager()->getStorage('currency');

    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = NULL;
    if ($element['#currency_code']) {
      $currency = $currency_storage->load($element['#currency_code']);
    }
    if (!$currency) {
      $currency = $currency_storage->load('XXX');
    }

    // Modify the element.
    $element['#tree'] = TRUE;
    $element['#theme_wrappers'][] = 'form_element';
    $element['#attached']['css'] = array(
      drupal_get_path('module', 'currency') . '/currency.css',
    );

    $signs = array_merge(array($currency->getSign()), $currency->getAlternativeSigns());
    $signs = array_combine($signs, $signs);
    $signs = array_unique(array_filter(array_merge(array(
      self::CUSTOM_VALUE => t('- Custom -'),
    ), $signs)));
    asort($signs);
    $element['sign'] = array(
      '#default_value' => in_array($element['#default_value'], $signs) ? $element['#default_value'] : self::CUSTOM_VALUE,
      '#empty_value' => '',
      '#options' => $signs,
      '#required' => $element['#required'],
      '#title' => t('Sign'),
      '#title_display' => 'invisible',
      '#type' => 'select',
    );
    $sign_js_selector = '.form-type-currency-sign .form-select';
    $element['sign_custom'] = array(
      '#attached' => array(
        'css' => array(
          drupal_get_path('module', 'currency') . '/currency.css',
        ),
      ),
      '#default_value' => $element['#default_value'],
      '#states' => array(
        'visible' => array(
          $sign_js_selector => array(
            'value' => self::CUSTOM_VALUE,
          ),
        ),
      ),
      '#title' => t('Custom sign'),
      '#title_display' => 'invisible',
      '#type' => 'textfield',
    );

    return $element;
  }

  /**
   * Implements form #element_validate callback.
   */
  public static function elementValidate($element, FormStateInterface $form_state, $form) {
    // Set a scalar value.
    $sign = $element['#value']['sign'];
    if ($sign == self::CUSTOM_VALUE) {
      $sign = $element['#value']['sign_custom'];
    }
    $form_state->setValueForElement($element, $sign);
  }

}
