<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternDeleteForm.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\ConfirmFormBase;

/**
 * Provides a currency locale pattern delete form.
 */
class CurrencyLocalePatternDeleteForm extends ConfirmFormBase {

  /**
   * The currency locale pattern to delete.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $currency_locale_pattern = NULL;

  /**
   * {@inheritdoc}
   */
  function getQuestion() {
    return t('Do you really want to delete @label?', array(
      '@label' => $this->currency_locale_pattern->label(),
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
  function getCancelPath() {
    return 'admin/config/regional/currency_locale_pattern';
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
    return 'currency_locale_pattern_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, EntityInterface $currency_locale_pattern = NULL) {
    $this->currency_locale_pattern = $currency_locale_pattern;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  function submitForm(array &$form, array &$form_state) {
    $this->currency_locale_pattern->delete();
    drupal_set_message(t('The locale pattern %label has been deleted.', array(
      '%label' => $this->currency_locale_pattern->label(),
    )));
    $form_state['redirect'] = $this->getCancelPath();
  }
}
