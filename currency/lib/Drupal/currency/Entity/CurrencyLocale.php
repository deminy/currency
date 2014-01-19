<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyLocale.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Language\LanguageManager;

/**
 * Defines a currency locale entity.
 *
 * @EntityType(
 *   config_prefix = "currency.currency_locale",
 *   controllers = {
 *     "access" = "Drupal\currency\Entity\CurrencyLocaleAccessController",
 *     "form" = {
 *       "default" = "Drupal\currency\Entity\CurrencyLocaleFormController",
 *       "delete" = "Drupal\currency\Entity\CurrencyLocaleDeleteFormController"
 *     },
 *     "list" = "Drupal\currency\Entity\CurrencyLocaleListController",
 *     "storage" = "Drupal\Core\Config\Entity\ConfigStorageController",
 *   },
 *   entity_keys = {
 *     "id" = "locale",
 *     "label" = "locale",
 *     "uuid" = "uuid",
 *     "status" = "status"
 *   },
 *   fieldable = FALSE,
 *   id = "currency_locale",
 *   label = @Translation("Currency currency locale"),
 *   links = {
 *     "canonical" = "currency_locale_edit",
 *     "create-form" = "currency_locale_add",
 *     "edit-form" = "currency_locale_edit"
 *   }
 * )
 */
class CurrencyLocale extends ConfigEntityBase implements CurrencyLocaleInterface {

  /**
   * The decimal separator character.
   *
   * @var string
   */
  protected $decimalSeparator = NULL;

  /**
   * The grouping separator character.
   *
   * @var string
   */
  protected $groupingSeparator = NULL;

  /**
   * The locale identifier.
   *
   * The identifier consists of a language code, an underscore, and a country
   * code. Examples: nl_NL, en_US.
   *
   * @var string
   */
  public $locale = NULL;

  /**
   * The Unicode CLDR number pattern.
   *
   * @var string
   */
  protected $pattern = NULL;

  /**
   * The UUID for this entity.
   *
   * @var string
   */
  public $uuid = NULL;

  /**
   * {@inheritdoc}
   */
  public function setDecimalSeparator($separator) {
    $this->decimalSeparator = $separator;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDecimalSeparator() {
    return $this->decimalSeparator;
  }

  /**
   * {@inheritdoc}
   */
  public function setGroupingSeparator($separator) {
    $this->groupingSeparator = $separator;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupingSeparator() {
    return $this->groupingSeparator;
  }

  /**
   * {@inheritdoc}
   */
  public function setLocale($language_code, $country_code) {
    $this->locale = strtolower($language_code) . '_' . strtoupper($country_code);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocale() {
    return $this->locale;
  }

  /**
   * {@inheritdoc}
   */
  public function setPattern($pattern) {
    $this->pattern = $pattern;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPattern() {
    return $this->pattern;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->locale;
  }

  /**
   * {@inheritdoc}
   */
  public function label($langcode = NULL) {
    list($language_code, $country_code) = explode('_', $this->locale);
    $languages = LanguageManager::getStandardLanguageList();
    $countries = \Drupal::service('country_manager')->getList();

    return t('@language (@country)', array(
      '@language' => isset($languages[$language_code]) ? $languages[$language_code][0] : $language_code,
      '@country' => isset($countries[$country_code]) ? $countries[$country_code] : $country_code,
    ), array(
      'langcode' => $langcode,
    ));
  }

  /**
   * Gets the language code.
   *
   * @return string
   */
  public function getLanguageCode() {
    if ($this->id()) {
      $fragments = explode('_', $this->id());
      return $fragments[0];
    }
  }

  /**
   * Gets the country code.
   *
   * @return string
   */
  public function getCountryCode() {
    if ($this->id()) {
      $fragments = explode('_', $this->id());
      return $fragments[1];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getExportProperties() {
    $properties['decimalSeparator'] = $this->getDecimalSeparator();
    $properties['groupingSeparator'] = $this->getGroupingSeparator();
    $properties['locale'] = $this->id();
    $properties['pattern'] = $this->getPattern();
    $properties['uuid'] = $this->uuid();

    return $properties;
  }
}
