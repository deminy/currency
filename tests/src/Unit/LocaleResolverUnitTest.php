<?php

/**
 * @file Contains \Drupal\Tests\currency\Unit\LocaleResolverUnitTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\Core\Language\Language;
use Drupal\currency\LocaleResolver;
use Drupal\currency\LocaleResolverInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\LocaleResolver
 *
 * @group Currency
 */
class LocaleResolverUnitTest extends UnitTestCase {

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
   * The event dispatcher.
   *
   * @var \Drupal\currency\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $eventDispatcher;

  /**
   * The language manager used for testing.
   *
   * @var \Drupal\Core\Language\LanguageManager|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $languageManager;

  /**
   * The class under test.
   *
   * @var \Drupal\currency\LocaleResolver
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->configFactory = $this->getMock('\Drupal\Core\Config\ConfigFactoryInterface');

    $this->currencyLocaleStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $this->entityManager->expects($this->any())
      ->method('getStorage')
      ->with('currency_locale')
      ->willReturn($this->currencyLocaleStorage);

    $this->eventDispatcher = $this->getMock('\Drupal\currency\EventDispatcherInterface');

    $this->languageManager = $this->getMock('\Drupal\Core\Language\LanguageManagerInterface');

    $this->sut = new LocaleResolver($this->entityManager, $this->languageManager, $this->configFactory, $this->eventDispatcher);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->sut = new LocaleResolver($this->entityManager, $this->languageManager, $this->configFactory, $this->eventDispatcher);
  }

  /**
   * @covers ::resolveCurrencyLocale
   */
  function testResolveCurrencyLocaleWithRequestCountry() {
    $this->prepareLanguageManager();

    $request_country_code = 'IN';
    $this->eventDispatcher->expects($this->atLeastOnce())
      ->method('resolveCountryCode')
      ->willReturn($request_country_code);

    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with($this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->getId() . '_' . $request_country_code)
      ->will($this->returnValue($currency_locale));

    // Test loading the fallback locale.
    $this->assertSame($currency_locale, $this->sut->resolveCurrencyLocale());
  }

  /**
   * @covers ::resolveCurrencyLocale
   */
  function testResolveCurrencyLocaleWithSiteDefaultCountry() {
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
      ->with($this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->getId() . '_' . $site_default_country)
      ->will($this->returnValue($currency_locale));

    // Test loading the fallback locale.
    $this->assertSame($currency_locale, $this->sut->resolveCurrencyLocale());
  }

  /**
   * @covers ::resolveCurrencyLocale
   */
  function testResolveCurrencyLocaleFallback() {
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

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with(LocaleResolverInterface::DEFAULT_LOCALE)
      ->will($this->returnValue($currency_locale));

    // Test loading the fallback locale.
    $this->assertSame($currency_locale, $this->sut->resolveCurrencyLocale());
  }

  /**
   * @covers ::resolveCurrencyLocale
   *
   * @expectedException \RuntimeException
   */
  function testResolveCurrencyLocaleMissingFallback() {
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

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with(LocaleResolverInterface::DEFAULT_LOCALE)
      ->willReturn(NULL);

    // Test loading the fallback locale.
    $this->sut->resolveCurrencyLocale();
  }

  /**
   * Prepares the language manager for testing.
   */
  protected function prepareLanguageManager() {
    $language_code = $this->randomMachineName(2);
    $language = $this->getMock('\Drupal\Core\Language\LanguageInterface');
    $language->expects($this->atLeastOnce())
      ->method('getId')
      ->willReturn($language_code);

    $this->languageManager->expects($this->any())
      ->method('getCurrentLanguage')
      ->with(Language::TYPE_CONTENT)
      ->will($this->returnValue($language));
  }

}
