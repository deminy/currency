<?php

/**
 * @file
 * Contains \Drupal\currency\LocaleDelegator.
 */

namespace Drupal\currency;

use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManager;
use Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern;

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
  public $countryCode = NULL;

  /**
   * A language manager.
   *
   * @var Drupal\Core\Language\LanguageManager
   */
  protected $languageManager = NULL;

  /**
   * The locale pattern to use.
   *
   * @var Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern
   */
  protected $localePattern;

  /**
   * Constructor.
   *
   * @param Drupal\Core\Language\LanguageManager $language_manager
   */
  function __construct(LanguageManager $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * Sets the locale pattern to use.
   *
   * @param $locale_pattern \Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern
   */
  function setLocalePattern(CurrencyLocalePattern $locale_pattern) {
    return $this->localePattern = $locale_pattern;
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
   * @throws RuntimeException
   *
   * @return \Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern
   */
  function getLocalePattern() {
    if (is_null($this->localePattern)) {
      $locale_pattern = NULL;
      $language_code = $this->languageManager->getLanguage(Language::TYPE_CONTENT)->langcode;

      // Try this request's country code.
      if ($this->countryCode) {
        $locale_pattern = entity_load('currency_locale_pattern', $language_code . '_' . $this->countryCode);
      }

      // Try the site's default country code.
      $country_code = config('system.data')->get('country.default');
      if (!$locale_pattern && $country_code) {
        $locale_pattern = entity_load('currency_locale_pattern', $language_code . '_' . $country_code);
      }

      // Try the Currency default.
      if (!$locale_pattern) {
        $locale_pattern = entity_load('currency_locale_pattern', $this::DEFAULT_LOCALE);
      }

      if ($locale_pattern) {
        $this->setLocalePattern($locale_pattern);
      }
      else {
        throw new \RuntimeException(t('The locale pattern for !default_locale could not be loaded.', array(
          '!default_locale' => $this::DEFAULT_LOCALE,
        )));
      }
    }

    return $this->localePattern;
  }

  /**
   * Resets the CurrencyLocalePattern that was loaded based on environment
   * variables.
   *
   * @return null
   */
  function resetLocalePattern() {
    $this->localePattern = NULL;
  }

  /**
   * Sets the currency locale's country for this request.
   *
   * @param string $country_code
   *   Any code that is also returned by country_get_list().
   *
   * @return null
   */
  function setCountryCode($country_code) {
    if ($this->countryCode != $country_code) {
      $this->countryCode = $country_code;
      $this->resetLocalePattern();
    }
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