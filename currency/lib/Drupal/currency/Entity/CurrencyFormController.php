<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyFormController.
 */

namespace Drupal\currency\Entity;

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
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = clone $this->getEntity($form_state);
    $currency->setCurrencyCode($values['currency_code']);
    $currency->setCurrencyNumber($values['currency_number']);
    $currency->setLabel($values['label']);
    $currency->setSign($values['sign']);
    $currency->setSubunits($values['subunits']);
    $currency->setRoundingStep($values['rounding_step']);
    $currency->setStatus($values['status']);

    return $currency;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */
    $currency = $this->getEntity();

    $form['currency_code'] = array(
      '#default_value' => $currency->id(),
      '#disabled' => (bool) $currency->id(),
      '#maxlength' => 3,
      '#pattern' => '[a-zA-Z]{3}',
      '#placeholder' => 'XXX',
      '#required' => TRUE,
      '#size' => 3,
      '#title' => t('ISO 4217 code'),
      '#type' => 'textfield',
    );

    $form['currency_number'] = array(
      '#default_value' => $currency->getCurrencyNumber(),
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
      '#default_value' => $currency->label(),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#title' => t('Name'),
      '#type' => 'textfield',
    );

    $form['sign'] = array(
      '#currency_code' => $currency->id(),
      '#default_value' => $currency->getSign(),
      '#title' => t('Sign'),
      '#type' => 'currency_sign',
    );

    $form['subunits'] = array(
      '#default_value' => $currency->getSubunits(),
      '#min' => 0,
      '#required' => TRUE,
      '#title' => t('Number of subunits'),
      '#type' => 'number',
    );

    $form['rounding_step'] = array(
      '#default_value' => $currency->getRoundingStep(),
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
    $form_state['redirect_route'] = array(
      'route_name' => 'currency.currency.delete',
      'route_parameters' => array(
        'currency' => $currency->id(),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    $currency_storage = \Drupal::entityManager()->getStorageController('currency');

    // Validate the currency code.
    $currency_code = $form['currency_code']['#value'];
    if (!preg_match('/[a-z]{3}/i', $currency_code)) {
      \Drupal::formBuilder()->setError($form['currency_code'], $form_state, $this->t('The currency code must be three letters.'));
    }
    elseif ($form['currency_code']['#default_value'] !== $currency_code) {
      $currency = $currency_storage->load($currency_code);
      if ($currency) {
        \Drupal::formBuilder()->setError($form['currency_code'], $form_state, $this->t('The currency code is already in use by !link.', array(
          '!link' => l($currency->label(), 'admin/config/regional/currency/' . $currency->id() . '/edit'),
        )));
      }
    }

    // Validate the currency number.
    $currency_number = $form['currency_number']['#value'];
    if ($currency_number && !preg_match('/\d{3}/i', $currency_number)) {
      \Drupal::formBuilder()->setError($form['currency_number'], $form_state, $this->t('The currency number must be three digits.'));
    }
    elseif ($form['currency_number']['#default_value'] !== $currency_number) {
      $currencies = $currency_storage->loadByProperties(array(
        'currencyNumber' => $currency_number,
      ));
      if ($currencies) {
        $currency = reset($currencies);
        \Drupal::formBuilder()->setError($form['currency_number'], $form_state, $this->t('The currency number is already in use by !link.', array(
          '!link' => l($currency->label(), 'admin/config/regional/currency/' . $currency->id() . '/edit'),
        )));
      }
    }
  }
}
