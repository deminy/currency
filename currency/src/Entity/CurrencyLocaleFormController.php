<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyLocaleFormController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Language\LanguageManager;

/**
 * Provides a currency_locale add/edit form.
 */
class CurrencyLocaleFormController extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, array &$form_state) {
    // @todo EntityFormController calls entity_form_submit_build_entity(),
    // which copies all top-level form state values to the entity. These values
    // include internal FAPI values and copying those pollutes the entity,
    // which is why we build the entity manually.
    $values = $form_state['values'];
    /** @var \Drupal\currency\Entity\CurrencyLocaleInterface $currency_locale */
    $currency_locale = clone $this->getEntity($form_state);
    $currency_locale->setLocale($values['language_code'], $values['country_code']);
    $currency_locale->setPattern($values['pattern']);
    $currency_locale->setDecimalSeparator($values['decimal_separator']);
    $currency_locale->setGroupingSeparator($values['grouping_separator']);

    return $currency_locale;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    /** @var \Drupal\currency\Entity\CurrencyLocaleInterface $currency_locale */
    $currency_locale = $this->getEntity();

    $form['locale'] = array(
      '#title' => t('Locale'),
      '#type' => 'details',
    );
    $options = array();
    foreach (LanguageManager::getStandardLanguageList() as $language_code => $language_names) {
      $options[$language_code] = $language_names[0];
    }
    natcasesort($options);
    $form['locale']['language_code'] = array(
      '#default_value' => $currency_locale->getLanguageCode(),
      '#empty_value' => '',
      '#options' => $options,
      '#required' => TRUE,
      '#title' => t('Language'),
      '#type' => 'select',
    );
    $form['locale']['country_code'] = array(
      '#default_value' => $currency_locale->getCountryCode(),
      '#empty_value' => '',
      '#options' => \Drupal::service('country_manager')->getList(),
      '#required' => TRUE,
      '#title' => t('Country'),
      '#type' => 'select',
    );
    $form['formatting'] = array(
      '#title' => t('Formatting'),
      '#type' => 'details',
    );
    $form['formatting']['decimal_separator'] = array(
      '#default_value' => $currency_locale->getDecimalSeparator(),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#size' => 3,
      '#title' => t('Decimal separator'),
      '#type' => 'textfield',
    );
    $form['formatting']['grouping_separator'] = array(
      '#default_value' => $currency_locale->getGroupingSeparator(),
      '#maxlength' => 255,
      '#size' => 3,
      '#title' => t('Group separator'),
      '#type' => 'textfield',
    );
    $form['formatting']['pattern'] = array(
      '#default_value' => $currency_locale->getPattern(),
      '#description' => t('A Unicode <abbr title="Common Locale Data Repository">CLDR</abbr> <a href="http://cldr.unicode.org/translation/number-patterns">currency number pattern</a>'),
      '#maxlength' => 255,
      '#title' => t('Pattern'),
      '#type' => 'textfield',
    );

    return parent::form($form, $form_state, $currency_locale);
  }

  /**
   * {@inheritdoc}
   */
  function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);
    // If this entity is new, its locale/ID has to be unique.
    $currency_locale = $this->buildEntity($form, $form_state);
    if ($currency_locale->isNew()) {
      $currency_locale_loaded = entity_load('currency_locale', $currency_locale->id());
      if ($currency_locale_loaded) {
        form_set_error('locale', t(' A pattern for this locale already exists.'));
      }
    }
  }

  /**
   * {@inheritdoc}.
   */
  public function save(array $form, array &$form_state) {
    $currency_locale = $this->getEntity($form_state);
    $currency_locale->save();
    drupal_set_message(t('The currency locale %label has been saved.', array(
      '%label' => $currency_locale->label(),
    )));
    $form_state['redirect_route'] = array(
      'route_name' => 'currency.currency_locale.list',
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function delete(array $form, array &$form_state) {
    $currency_locale = $this->getEntity($form_state);
    $form_state['redirect_route'] = array(
      'route_name' => 'currency.currency_locale.delete',
      'route_parameters' => array(
        'currency_locale' => $currency_locale->id(),
      ),
    );
  }
}
