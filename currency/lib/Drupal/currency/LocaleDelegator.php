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
 * 
 */
class LocaleDelegator {

  /**
   * The default locale.
   */
  const DEFAULT_LOCALE = 'en_US';

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
   * Sets the currency locale to use.
   *
   * @param \Drupal\currency\Entity\CurrencyLocaleInterface $currency_locale
   *
   * @return $this
   */
  function setCurrencyLocale(CurrencyLocaleInterface $currency_locale) {
    $this->currencyLocale = $currency_locale;

    return $this;
  }

  /**
   * Loads the currency locale to use.
   *
   * If no currency locale was explicitly set, it will load one based on the
   * environment. If no country code is set using self::setCountryCode(), the
   * "site_default_country" system variable will be used instead. If a
   * CurrencyLocale could not be loaded using these country sources and
   * $language->language, the currency locale for en_US will be loaded. This is
   * consistent with Drupal's default language, which is US English.
   *
   * @throws \RuntimeException
   *
   * @return \Drupal\currency\Entity\CurrencyLocaleInterface
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
   * Resets the CurrencyLocale that was loaded based on environment
   * variables.
   *
   * @return $this
   */
  function resetCurrencyLocale() {
    $this->currencyLocale = NULL;

    return $this;
  }

  /**
   * Sets the currency locale's country for this request.
   *
   * @param string $country_code
   *   Any code that is also returned by country_get_list().
   *
   * @return $this
   */
  function setCountryCode($country_code) {
    if ($this->countryCode != $country_code) {
      $this->countryCode = $country_code;
      $this->resetCurrencyLocale();
    }

    return $this;
  }

  /**
   * Gets the currency locale's country for this request.
   *
   * @return null|string
   */
  function getCountryCode() {
    return $this->countryCode;
  }
}