<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternDeleteFormController.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Entity\EntityConfirmFormBase;

/**
 * Provides a currency locale pattern delete form.
 */
class CurrencyLocalePatternDeleteFormController extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you really want to delete @label?', array(
      '@label' => $this->getEntity()->label(),
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelPath() {
    return 'admin/config/regional/currency_locale_pattern';
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
  public function getFormID() {
    return 'currency_locale_pattern_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $currency_locale_pattern = $this->getEntity();
    $currency_locale_pattern->delete();
    drupal_set_message(t('The locale pattern %label has been deleted.', array(
      '%label' => $currency_locale_pattern->label(),
    )));
    $form_state['redirect'] = $this->getCancelPath();
  }
}
