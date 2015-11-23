<?php

/**
 * @file Contains \Drupal\Tests\currency\Unit\LocaleResolverTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\currency\EventDispatcherInterface;
use Drupal\currency\LocaleResolver;
use Drupal\currency\LocaleResolverInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\LocaleResolver
 *
 * @group Currency
 */
class LocaleResolverTest extends UnitTestCase {

  /**
   * The config factory.
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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher.
   *
   * @var \Drupal\currency\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $eventDispatcher;

  /**
   * The language manager.
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
    $this->configFactory = $this->getMock(ConfigFactoryInterface::class);

    $this->currencyLocaleStorage = $this->getMock(EntityStorageInterface::class);

    $this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
    $this->entityTypeManager->expects($this->any())
      ->method('getStorage')
      ->with('currency_locale')
      ->willReturn($this->currencyLocaleStorage);

    $this->eventDispatcher = $this->getMock(EventDispatcherInterface::class);

    $this->languageManager = $this->getMock(LanguageManagerInterface::class);

    $this->sut = new LocaleResolver($this->entityTypeManager, $this->languageManager, $this->configFactory, $this->eventDispatcher);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->sut = new LocaleResolver($this->entityTypeManager, $this->languageManager, $this->configFactory, $this->eventDispatcher);
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

    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with($this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->getId() . '_' . $request_country_code)
      ->willReturn($currency_locale);

    // Test loading the fallback locale.
    $this->assertSame($currency_locale, $this->sut->resolveCurrencyLocale());
  }

  /**
   * @covers ::resolveCurrencyLocale
   */
  function testResolveCurrencyLocaleWithSiteDefaultCountry() {
    $this->prepareLanguageManager();

    $site_default_country = 'IN';

    $config = $this->getMockBuilder(Config::class)
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->any())
      ->method('get')
      ->with('country.default')
      ->willReturn($site_default_country);

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('system.data')
      ->willReturn($config);

    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with($this->languageManager->getCurrentLanguage(Language::TYPE_CONTENT)->getId() . '_' . $site_default_country)
      ->willReturn($currency_locale);

    // Test loading the fallback locale.
    $this->assertSame($currency_locale, $this->sut->resolveCurrencyLocale());
  }

  /**
   * @covers ::resolveCurrencyLocale
   */
  function testResolveCurrencyLocaleFallback() {
    $this->prepareLanguageManager();

    $config = $this->getMockBuilder(Config::class)
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->any())
      ->method('get')
      ->with('country.default')
      ->willReturn(NULL);

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('system.data')
      ->willReturn($config);

    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);

    $this->currencyLocaleStorage->expects($this->any())
      ->method('load')
      ->with(LocaleResolverInterface::DEFAULT_LOCALE)
      ->willReturn($currency_locale);

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

    $config = $this->getMockBuilder(Config::class)
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->any())
      ->method('get')
      ->with('country.default')
      ->willReturn(NULL);

    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('system.data')
      ->willReturn($config);

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
    $language = $this->getMock(LanguageInterface::class);
    $language->expects($this->atLeastOnce())
      ->method('getId')
      ->willReturn($language_code);

    $this->languageManager->expects($this->any())
      ->method('getCurrentLanguage')
      ->with(Language::TYPE_CONTENT)
      ->willReturn($language);
  }

}
