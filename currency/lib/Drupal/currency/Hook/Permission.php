<?php

/**
 * @file
 * Contains \Drupal\currency\Hook\Permission.
 */

namespace Drupal\currency\Hook;

use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Implements hook_permission().
 *
 * @see currency_permission()
 */
class Permission {

  /**
   * The translation manager service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $translationManager;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation_manager
   */
  public function __construct(TranslationInterface $translation_manager) {
    $this->translationManager = $translation_manager;
  }

  /**
   * Invokes the implementation.
   */
  public function invoke() {
    $permissions['currency.amount_formatting.administer'] = array(
      'title' => $this->t('Administer amount formatting'),
    );
    $permissions['currency.exchange_rate_provider.administer'] = array(
      'title' => $this->t('Administer currency exchange rate providers'),
    );
    $permissions['currency.exchange_rate_provider.fixed_rates.administer'] = array(
      'title' => $this->t('Administer fixed exchange rates'),
    );
    $permissions['currency.currency.view'] = array(
      'title' => $this->t('View currencies'),
    );
    $permissions['currency.currency.create'] = array(
      'title' => $this->t('Add new currencies'),
    );
    $permissions['currency.currency.update'] = array(
      'title' => $this->t('Edit currencies'),
    );
    $permissions['currency.currency.delete'] = array(
      'title' => $this->t('Delete currencies'),
    );
    $permissions['currency.currency_locale.view'] = array(
      'title' => $this->t('View currency locales'),
    );
    $permissions['currency.currency_locale.create'] = array(
      'restrict access' => TRUE,
      'title' => $this->t('Add new currency locales'),
    );
    $permissions['currency.currency_locale.update'] = array(
      'restrict access' => TRUE,
      'title' => $this->t('Edit currency locales'),
    );
    $permissions['currency.currency_locale.delete'] = array(
      'title' => $this->t('Delete currency locales'),
    );

    return $permissions;
  }

  /**
   * Translates a string to the current language or to a given language.
   *
   * See the t() documentation for details.
   */
  protected function t($string, array $args = array(), array $options = array()) {
    return $this->translationManager->translate($string, $args, $options);
  }

}
