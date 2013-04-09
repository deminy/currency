<?php

/**
 * @file
 * Definition of Drupal\currency\Controller\CurrencyDeleteForm.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\ConfirmFormBase;

/**
 * Provides a currency delete form.
 */
class CurrencyDeleteForm extends ConfirmFormBase {

  /**
   * The currency to delete.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $currency = NULL;

  /**
   * {@inheritdoc}
   */
  function getQuestion() {
    return t('Do you really want to delete @label?', array(
      '@label' => $this->currency->label(),
    ));
  }

  /**
   * {@inheritdoc}
   */
  function getCancelPath() {
    return 'admin/config/regional/currency';
  }

  /**
   * {@inheritdoc}
   */
  protected function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  function getFormID() {
    return 'currency_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, EntityInterface $currency = NULL) {
    $this->currency = $currency;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  function submitForm(array &$form, array &$form_state) {
    $this->currency->delete();
    drupal_set_message(t('The currency %label has been deleted.', array(
      '%label' => $currency->label(),
    )));
    $form_state['redirect'] = $this->getCancelPath();
  }
}
