<?php

/**
 * @file
 * Definition of Drupal\currency\FormHelper.
 */

namespace Drupal\currency;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Provides form helpers.
 */
class FormHelper implements FormHelperInterface {

  use StringTranslationTrait;

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $currencyStorage;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   */
  public function __construct(TranslationInterface $string_translation, EntityManagerInterface $entity_manager) {
    $this->currencyStorage = $entity_manager->getStorage('currency');
    $this->stringTranslation = $string_translation;
  }


  /**
   * {@inheritdoc}
   */
  public function getCurrencyOptions() {
    $options = array();
    /** @var \Drupal\currency\Entity\CurrencyInterface[] $currencies */
    $currencies = $this->currencyStorage->loadMultiple();
    foreach ($currencies as $currency) {
      // Do not show disabled currencies.
      if ($currency->status()) {
        $options[$currency->id()] = $this->t('@currency_title (@currency_code)', array(
          '@currency_title' => $currency->label(),
          '@currency_code' => $currency->id(),
        ));
      }
    }
    natcasesort($options);

    return $options;
  }

}
