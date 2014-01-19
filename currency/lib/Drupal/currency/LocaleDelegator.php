<?php

/**
 * @file
 * Contains \Drupal\currency\LocaleDelegator.
 */

namespace Drupal\currency;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManager;
use Drupal\currency\Entity\CurrencyLocaleInterface;

/**
 * Gets the right locale for the environment.
 */
class LocaleDelegator implements LocaleDelegatorInterface {

  /**
   * The ISO 3166 code of the country to use for locale loading.
   *
   * @var string
   */
  protected $countryCode;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The currency currency locale storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageControllerInterface
   */
  protected $currencyLocaleStorage;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * The currency locale to use.
   *
   * @var \Drupal\currency\Entity\CurrencyLocale
   */
  protected $currencyLocale;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   */
  function __construct(EntityManagerInterface $entity_manager, LanguageManager $language_manager, ConfigFactory $config_factory) {
    $this->configFactory = $config_factory;
    $this->currencyLocaleStorage = $entity_manager->getStorageController('currency_locale');
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  function setCurrencyLocale(CurrencyLocaleInterface $currency_locale) {
    $this->currencyLocale = $currency_locale;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  function getCurrencyLocale() {
    if (is_null($this->currencyLocale)) {
      $currency_locale = NULL;
      $language_code = $this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->id;

      // Try this request's country code.
      if ($this->getCountryCode()) {
        $currency_locale = $this->currencyLocaleStorage->load($language_code . '_' . $this->countryCode);
      }

      // Try the site's default country code.
      if (!$currency_locale) {
        $country_code = $this->configFactory->get('system.data')->get('country.default');
        if ($country_code) {
          $currency_locale = $this->currencyLocaleStorage->load($language_code . '_' . $country_code);
        }

      }

      // Try the Currency default.
      if (!$currency_locale) {
        $currency_locale = $this->currencyLocaleStorage->load($this::DEFAULT_LOCALE);
      }

      if ($currency_locale) {
        $this->setCurrencyLocale($currency_locale);
      }
      else {
        throw new \RuntimeException('The currency locale for ' . $this::DEFAULT_LOCALE . ' could not be loaded.');
      }
    }

    return $this->currencyLocale;
  }

  /**
   * {@inheritdoc}
   */
  function resetCurrencyLocale() {
    $this->currencyLocale = NULL;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  function setCountryCode($country_code) {
    if ($this->countryCode != $country_code) {
      $this->countryCode = $country_code;
      $this->resetCurrencyLocale();
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  function getCountryCode() {
    return $this->countryCode;
  }
}