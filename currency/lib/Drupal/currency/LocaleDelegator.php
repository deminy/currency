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
use Drupal\currency\Entity\CurrencyLocalePatternInterface;

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
   * The currency locale pattern storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageControllerInterface
   */
  protected $currencyLocalePatternStorage;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * The locale pattern to use.
   *
   * @var \Drupal\currency\Entity\CurrencyLocalePattern
   */
  protected $localePattern;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   */
  function __construct(EntityManagerInterface $entity_manager, LanguageManager $language_manager, ConfigFactory $config_factory) {
    $this->configFactory = $config_factory;
    $this->currencyLocalePatternStorage = $entity_manager->getStorageController('currency_locale_pattern');
    $this->languageManager = $language_manager;
  }

  /**
   * Sets the locale pattern to use.
   *
   * @param \Drupal\currency\Entity\CurrencyLocalePatternInterface $locale_pattern
   *
   * @return $this
   */
  function setLocalePattern(CurrencyLocalePatternInterface $locale_pattern) {
    $this->localePattern = $locale_pattern;

    return $this;
  }

  /**
   * Loads the locale pattern to use.
   *
   * If no locale pattern was explicitly set, it will load one based on the
   * environment. If no country code is set using self::setCountryCode(), the
   * "site_default_country" system variable will be used instead. If a
   * CurrencyLocalePattern could not be loaded using these country sources and
   * $language->language, the locale pattern for en_US will be loaded. This is
   * consistent with Drupal's default language, which is US English.
   *
   * @throws \RuntimeException
   *
   * @return \Drupal\currency\Entity\CurrencyLocalePatternInterface
   */
  function getLocalePattern() {
    if (is_null($this->localePattern)) {
      $locale_pattern = NULL;
      $language_code = $this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->id;

      // Try this request's country code.
      if ($this->getCountryCode()) {
        $locale_pattern = $this->currencyLocalePatternStorage->load($language_code . '_' . $this->countryCode);
      }

      // Try the site's default country code.
      if (!$locale_pattern) {
        $country_code = $this->configFactory->get('system.data')->get('country.default');
        if ($country_code) {
          $locale_pattern = $this->currencyLocalePatternStorage->load($language_code . '_' . $country_code);
        }

      }

      // Try the Currency default.
      if (!$locale_pattern) {
        $locale_pattern = $this->currencyLocalePatternStorage->load($this::DEFAULT_LOCALE);
      }

      if ($locale_pattern) {
        $this->setLocalePattern($locale_pattern);
      }
      else {
        throw new \RuntimeException('The locale pattern for ' . $this::DEFAULT_LOCALE . ' could not be loaded.');
      }
    }

    return $this->localePattern;
  }

  /**
   * Resets the CurrencyLocalePattern that was loaded based on environment
   * variables.
   *
   * @return $this
   */
  function resetLocalePattern() {
    $this->localePattern = NULL;

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
      $this->resetLocalePattern();
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