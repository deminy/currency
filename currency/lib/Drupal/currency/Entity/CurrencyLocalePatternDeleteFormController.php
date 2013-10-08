<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyLocalePatternDeleteFormController.
 */

namespace Drupal\currency\Entity;

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
  public function getCancelRoute() {
    return array(
      'route_name' => 'currency_locale_pattern_list',
    );
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
