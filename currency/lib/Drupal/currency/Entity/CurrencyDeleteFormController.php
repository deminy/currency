<?php

/**
 * @file
 * Contains Drupal\currency\Entity\CurrencyDeleteFormController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Entity\EntityConfirmFormBase;

/**
 * Provides the payment method deletion form.
 */
class CurrencyDeleteFormController extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $currency = $this->getEntity();

    return t('Do you really want to delete %label?', array(
      '%label' => $currency->label(),
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelPath() {
    return 'admin/config/regional/currency';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'currency_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $currency = $this->getEntity();
    $currency->delete();
    drupal_set_message(t('The %label has been deleted.', array(
      '%label' => $currency->label(),
    )));
    $form_state['redirect'] = $this->getCancelPath();
  }
}
