<?php

/**
 * @file Contains \Drupal\currency\Tests\LocaleDelegatorUnitTest.
 */

namespace Drupal\currency\Tests;

use Drupal\Core\Language\Language;
use Drupal\currency\LocaleDelegator;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\LocaleDelegator.
 */
class LocaleDelegatorUnitTest extends UnitTestCase {

  /**
   * The config factory used for testing.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * The currency currency locale storage used for testing
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyLocaleStorage;

  /**
   * The entity manager used for testing.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityManager;

  /**
   * The language manager used for testing.
   *
   * @var \Drupal\Core\Language\LanguageManager|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $languageManager;

  /**
   * The locale delegator under test.
   *
   * @var \Drupal\currency\LocaleDelegator
   */
  protected $localeDelegator;

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\LocaleDelegator unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->configFactory = $this->getMockBuilder('\Drupal\Core\Config\ConfigFactory')
      ->disableOriginalConstructor()
      ->getMock();

    $this->currencyLocaleStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $this->entityManager->expects($this->any())
      ->method('getStorage')
      ->with('currency_locale')
      ->will($this->returnValue($this->currencyLocaleStorage));

    $this->languageManager = $this->getMockBuilder('\Drupal\Core\Language\LanguageManager')
      ->disableOriginalConstructor()
      ->getMock();

    $this->localeDelegator = new LocaleDelegator($this->entityManager, $this->languageManager, $this->configFactory);
  }

  /**
   * Tests setCountryCode() and getCountryCode().
   */
  function testGetCountryCode() {
    $country_code = $this->randomName(2);

    // Test getting the default.
    $this->assertNull($this->localeDelegator->getCountryCode());

    // Test setting a custom country.
    $this->assertSame(spl_object_hash($this->localeDelegator), spl_object_hash($this->localeDelegator->setCountryCode($country_code)));
    $this->assertSame($country_code, $this->localeDelegator->getCountryCode());
  }

  /**
   * Tests getCurrencyLocale().
   *
   * @depends testGetCountryCode
   */
  function testGetCurrencyLocaleWithRequestCountry() {
    $this->prepareLanguageManager();

    $request_country_code = 'IN';
    $this->localeDelegator->setCountryCode($request_country_code);

    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with($this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->id . '_' . $request_country_code)
      ->will($this->returnValue($currency_locale));

    // Test loading the fallback locale.
    $this->assertSame(spl_object_hash($currency_locale), spl_object_hash($this->localeDelegator->getCurrencyLocale()));
  }

  /**
   * Tests getCurrencyLocale().
   */
  function testGetCurrencyLocaleWithSiteDefaultCountry() {
    $this->prepareLanguageManager();

    $site_default_country = 'IN';

    $config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->any())
      ->method('get')
      ->with('country.default')
      ->will($this->returnValue($site_default_country));

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('system.data')
      ->will($this->returnValue($config));

    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with($this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->id . '_' . $site_default_country)
      ->will($this->returnValue($currency_locale));

    // Test loading the fallback locale.
    $this->assertSame(spl_object_hash($currency_locale), spl_object_hash($this->localeDelegator->getCurrencyLocale()));
  }

  /**
   * Tests getCurrencyLocale().
   */
  function testGetCurrencyLocaleFallback() {
    $this->prepareLanguageManager();

    $config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->any())
      ->method('get')
      ->with('country.default')
      ->will($this->returnValue(NULL));

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('system.data')
      ->will($this->returnValue($config));

    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

    $delegator = $this->localeDelegator;
    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with($delegator::DEFAULT_LOCALE)
      ->will($this->returnValue($currency_locale));

    // Test loading the fallback locale.
    $this->assertSame(spl_object_hash($currency_locale), spl_object_hash($this->localeDelegator->getCurrencyLocale()));
  }

  /**
   * Prepares the language manager for testing.
   */
  protected function prepareLanguageManager() {
    $language_code = $this->randomName(2);
    $language = $this->getMockBuilder('\Drupal\Core\Language\Language')
      ->disableOriginalConstructor()
      ->getMock();
    $language->id = $language_code;

    $this->languageManager->expects($this->any())
      ->method('getCurrentLanguage')
      ->with(Language::TYPE_CONTENT)
      ->will($this->returnValue($language));
  }
}
