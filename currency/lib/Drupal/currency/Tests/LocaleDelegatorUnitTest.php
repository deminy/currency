<?php

/**
 * @file Contains \Drupal\currency\Tests\LocaleDelegatorUnitTest.
 */

namespace Drupal\currency\Tests;

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
   * The currency locale pattern storage used for testing
   *
   * @var \Drupal\Core\Entity\EntityStorageControllerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyLocalePatternStorage;

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

    $this->currencyLocalePatternStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageControllerInterface');

    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $this->entityManager->expects($this->any())
      ->method('getStorageController')
      ->with('currency_locale_pattern')
      ->will($this->returnValue($this->currencyLocalePatternStorage));

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
   * Tests getLocalePattern().
   *
   * @depends testGetCountryCode
   */
  function testGetLocalePatternWithRequestCountry() {
    $this->prepareLanguageManager();

    $request_country_code = 'IN';
    $this->localeDelegator->setCountryCode($request_country_code);

    $locale_pattern = $this->getMock('\Drupal\currency\Entity\CurrencyLocalePatternInterface');

    $this->currencyLocalePatternStorage->expects($this->any())
      ->method('load')
      ->with($this->languageManager->getLanguage()->id . '_' . $request_country_code)
      ->will($this->returnValue($locale_pattern));

    // Test loading the fallback locale.
    $this->assertSame(spl_object_hash($locale_pattern), spl_object_hash($this->localeDelegator->getLocalePattern()));
  }

  /**
   * Tests getLocalePattern().
   */
  function testGetLocalePatternWithSiteDefaultCountry() {
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

    $locale_pattern = $this->getMock('\Drupal\currency\Entity\CurrencyLocalePatternInterface');

    $this->currencyLocalePatternStorage->expects($this->any())
      ->method('load')
      ->with($this->languageManager->getLanguage()->id . '_' . $site_default_country)
      ->will($this->returnValue($locale_pattern));

    // Test loading the fallback locale.
    $this->assertSame(spl_object_hash($locale_pattern), spl_object_hash($this->localeDelegator->getLocalePattern()));
  }

  /**
   * Tests getLocalePattern().
   */
  function testGetLocalePatternFallback() {
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

    $locale_pattern = $this->getMock('\Drupal\currency\Entity\CurrencyLocalePatternInterface');

    $delegator = $this->localeDelegator;
    $this->currencyLocalePatternStorage->expects($this->any())
      ->method('load')
      ->with($delegator::DEFAULT_LOCALE)
      ->will($this->returnValue($locale_pattern));

    // Test loading the fallback locale.
    $this->assertSame(spl_object_hash($locale_pattern), spl_object_hash($this->localeDelegator->getLocalePattern()));
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
      ->method('getLanguage')
      ->will($this->returnValue($language));
  }
}
