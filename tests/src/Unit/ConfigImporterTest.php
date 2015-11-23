<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\ConfigImporterTest.
 */

namespace Drupal\Tests\currency\Unit;

use Commercie\Currency\ResourceRepository;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\currency\ConfigImporter;
use Drupal\currency\Entity\CurrencyInterface;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \Drupal\currency\ConfigImporter
 *
 * @group Currency
 */
class ConfigImporterTest extends UnitTestCase {

  /**
   * The config storage.
   *
   * @var \Drupal\Core\Config\StorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configStorage;

  /**
   * The currency entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The currency locale entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyLocaleStorage;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $eventDispatcher;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The class under test.
   *
   * @var \Drupal\currency\ConfigImporter
   */
  protected $sut;

  /**
   * The typed config manager.
   *
   * @var \Drupal\Core\Config\TypedConfigManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $typedConfigManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->configStorage = $this->getMock(StorageInterface::class);

    $this->currencyStorage = $this->getMock(EntityStorageInterface::class);

    $this->currencyLocaleStorage = $this->getMock(EntityStorageInterface::class);

    $this->eventDispatcher = $this->getMock(EventDispatcherInterface::class);

    $this->moduleHandler = $this->getMock(ModuleHandlerInterface::class);

    $this->typedConfigManager = $this->getMock(TypedConfigManagerInterface::class);

    $map = [
      ['currency', $this->currencyStorage],
      ['currency_locale', $this->currencyLocaleStorage],
    ];
    $this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
    $this->entityTypeManager->expects($this->atLeastOnce())
      ->method('getStorage')
      ->willReturnMap($map);

    $this->sut = new ConfigImporter($this->moduleHandler, $this->eventDispatcher, $this->typedConfigManager, $this->entityTypeManager);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->sut = new ConfigImporter($this->moduleHandler, $this->eventDispatcher, $this->typedConfigManager, $this->entityTypeManager);
  }

  /**
   * @covers ::getConfigStorage
   * @covers ::setConfigStorage
   */
  public function testGetConfigStorage() {
    $method_get = new \ReflectionMethod($this->sut, 'getConfigStorage');
    $method_get->setAccessible(TRUE);

    $extension = new Extension($this->randomMachineName(), $this->randomMachineName(), $this->randomMachineName());

    $this->moduleHandler->expects($this->atLeastOnce())
      ->method('getModule')
      ->willReturn($extension);

    $this->assertInstanceof(StorageInterface::class, $method_get->invoke($this->sut));

    $config_storage = $this->getMock(StorageInterface::class);
    $this->sut->setConfigStorage($config_storage);
    $this->assertSame($config_storage, $method_get->invoke($this->sut));
  }

  /**
   * @covers ::getImportableCurrencies
   * @covers ::createCurrencyFromRepository
   */
  public function testGetImportableCurrencies() {
    $resource_repository = new ResourceRepository();
    $resource_repository_currency_codes = $resource_repository->listCurrencies();

    // Fake the importable and existing currencies, by taking two mutually
    // exclusive, randomized samples of the currencies available in the
    // repository.
    shuffle($resource_repository_currency_codes);
    list($importable_currency_codes, $stored_currency_codes) = array_chunk($resource_repository_currency_codes, ceil(count($resource_repository_currency_codes) / 2));

    $currency = $this->getMock(CurrencyInterface::class);

    $stored_currencies = [];
    foreach ($stored_currency_codes as $stored_currency_code) {
      $stored_currencies[$stored_currency_code] = $this->getMock(CurrencyInterface::class);
    }

    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('create')
      ->willReturn($currency);
    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('loadMultiple')
      ->with(NULL)
      ->willReturn($stored_currencies);

    $importable_currencies = $this->sut->getImportableCurrencies();
    sort($importable_currency_codes);
    $this->assertEquals($importable_currency_codes, array_keys($importable_currencies));
  }

  /**
   * @covers ::getImportableCurrencyLocales
   */
  public function testGetImportableCurrencyLocales() {
    $locale_a = $this->randomMachineName();
    $currency_locale_a = $this->getMock(CurrencyLocaleInterface::class);

    $locale_b = $this->randomMachineName();
    $currency_locale_b = $this->getMock(CurrencyLocaleInterface::class);

    $locale_c = $this->randomMachineName();
    $currency_locale_data_c = [
      'id' => $locale_c,
    ];
    $currency_locale_c = $this->getMock(CurrencyLocaleInterface::class);

    $stored_currencies = [
      $locale_a => $currency_locale_a,
      $locale_b => $currency_locale_b,
    ];

    $this->currencyLocaleStorage->expects($this->atLeastOnce())
      ->method('create')
      ->with($currency_locale_data_c)
      ->willReturn($currency_locale_c);
    $this->currencyLocaleStorage->expects($this->atLeastOnce())
      ->method('loadMultiple')
      ->with(NULL)
      ->willReturn($stored_currencies);

    $prefix = 'currency.currency_locale.';
    $this->configStorage->expects($this->atLeastOnce())
      ->method('listAll')
      ->with($prefix)
      ->willReturn([$prefix . $locale_b, $prefix . $locale_c]);
    $this->configStorage->expects($this->once())
      ->method('read')
      ->with($prefix . $locale_c)
      ->willReturn($currency_locale_data_c);

    $this->sut->setConfigStorage($this->configStorage);

    $importable_currencies = $this->sut->getImportableCurrencyLocales();
    $this->assertSame([$currency_locale_c], $importable_currencies);
  }

  /**
   * @covers ::importCurrency
   * @covers ::createCurrencyFromRepository
   */
  public function testImportCurrency() {
    $resource_repository = new ResourceRepository();
    $currency_codes = $resource_repository->listCurrencies();
    $currency_code = $currency_codes[array_rand($currency_codes)];

    $currency = $this->getMock(CurrencyInterface::class);

    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('create')
      ->with()
      ->willReturn($currency);

    $this->sut->setConfigStorage($this->configStorage);

    $this->assertSame($currency, $this->sut->importCurrency($currency_code));
  }

  /**
   * @covers ::importCurrency
   */
  public function testImportCurrencyWithExistingCurrency() {
    $currency_code = $this->randomMachineName();
    $currency = $this->getMock(CurrencyInterface::class);

    $this->currencyStorage->expects($this->never())
      ->method('create');
    $this->currencyStorage->expects($this->once())
      ->method('load')
      ->with($currency_code)
      ->willReturn($currency);

    $this->configStorage->expects($this->never())
      ->method('read');

    $this->sut->setConfigStorage($this->configStorage);

    $this->assertFalse($this->sut->importCurrency($currency_code));
  }

  /**
   * @covers ::importCurrencyLocale
   */
  public function testImportCurrencyLocale() {
    $locale = $this->randomMachineName();
    $currency_locale_data = [
      'id' => $locale,
    ];
    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);

    $this->currencyLocaleStorage->expects($this->atLeastOnce())
      ->method('create')
      ->with($currency_locale_data)
      ->willReturn($currency_locale);

    $this->configStorage->expects($this->once())
      ->method('read')
      ->with('currency.currency_locale.' . $locale)
      ->willReturn($currency_locale_data);

    $this->sut->setConfigStorage($this->configStorage);

    $this->assertSame($currency_locale, $this->sut->importCurrencyLocale($locale));
  }

  /**
   * @covers ::importCurrencyLocale
   */
  public function testImportCurrencyLocaleWithExistingCurrency() {
    $locale = $this->randomMachineName();
    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);

    $this->currencyLocaleStorage->expects($this->never())
      ->method('create');
    $this->currencyLocaleStorage->expects($this->once())
      ->method('load')
      ->with($locale)
      ->willReturn($currency_locale);

    $this->configStorage->expects($this->never())
      ->method('read');

    $this->sut->setConfigStorage($this->configStorage);

    $this->assertFalse($this->sut->importCurrencyLocale($locale));
  }

}
