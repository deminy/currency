<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyFormController.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityFormController;

/**
 * Provides a currency add/edit form.
 */
class CurrencyFormController extends EntityFormController {

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, array &$form_state) {
    // @todo EntityFormController calls entity_form_submit_build_entity(),
    // which copies all top-level form state values to the entity. These values
    // include internal FAPI values and copying those pollutes the entity,
    // which is why we build the entity manually.
    $values = $form_state['values'];
    $currency = clone $this->getEntity($form_state);
    $currency->currencyCode = $values['currency_code'];
    $currency->currencyNumber = $values['currency_number'];
    $currency->label = $values['label'];
    $currency->sign = $values['sign'];
    $currency->subunits = $values['subunits'];
    $currency->roundingStep = $values['rounding_step'];
    $currency->setStatus($values['status']);

    return $currency;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $currency = $this->getEntity();

    $form['currency_code'] = array(
      '#default_value' => $currency->currencyCode,
      '#disabled' => (bool) $currency->id(),
      '#element_validate' => array('currency_form_element_validate_iso_4217_code'),
      '#maxlength' => 3,
      '#pattern' => '[a-zA-Z]{3}',
      '#placeholder' => 'XXX',
      '#required' => TRUE,
      '#size' => 3,
      '#title' => t('ISO 4217 code'),
      '#type' => 'textfield',
    );

    // @todo Make sure that no other currency with this number already exists
    // when adding a new currency.
    $form['currency_number'] = array(
      '#default_value' => $currency->currencyNumber,
      '#element_validate' => array('currency_form_element_validate_iso_4217_number'),
      '#maxlength' => 3,
      '#pattern' => '[\d]{3}',
      '#placeholder' => '999',
      '#size' => 3,
      '#title' => t('ISO 4217 number'),
      '#type' => 'textfield',
    );

    $form['status'] = array(
      '#default_value' => $currency->status(),
      '#title' => t('Enabled'),
      '#type' => 'checkbox',
    );

    $form['label'] = array(
      '#default_value' => $currency->label,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#title' => t('Name'),
      '#type' => 'textfield',
    );

    $form['sign'] = array(
      '#currency_code' => $currency->currencyCode,
      '#default_value' => $currency->sign,
      '#title' => t('Sign'),
      '#type' => 'currency_sign',
    );

    $form['subunits'] = array(
      '#default_value' => $currency->subunits,
      '#min' => 0,
      '#required' => TRUE,
      '#title' => t('Number of subunits'),
      '#type' => 'number',
    );

    $form['rounding_step'] = array(
      '#default_value' => $currency->roundingStep,
      '#min' => 0,
      '#required' => TRUE,
      '#title' => t('Rounding step'),
      '#type' => 'number',
    );

    return parent::form($form, $form_state, $currency);
  }

  /**
   * {@inheritdoc}.
   */
  public function save(array $form, array &$form_state) {
    $currency = $this->getEntity($form_state);
    $currency->save();
    drupal_set_message(t('The currency %label has been saved.', array(
      '%label' => $currency->label(),
    )));
    $form_state['redirect'] = 'admin/config/regional/currency';
  }

  /**
   * {@inheritdoc}.
   */
  public function delete(array $form, array &$form_state) {
    $currency = $this->getEntity($form_state);
    $form_state['redirect'] = 'admin/config/regional/currency/' . $currency->id() . '/delete';
  }
}
