<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\ConfigImporterTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
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
   * The config importer under test.
   *
   * @var \Drupal\currency\ConfigImporter
   */
  protected $configImporter;

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
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

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
    $this->entityManager = $this->getMock(EntityManagerInterface::class);
    $this->entityManager->expects($this->atLeastOnce())
      ->method('getStorage')
      ->willReturnMap($map);

    $this->configImporter = new ConfigImporter($this->moduleHandler, $this->eventDispatcher, $this->typedConfigManager, $this->entityManager);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->configImporter = new ConfigImporter($this->moduleHandler, $this->eventDispatcher, $this->typedConfigManager, $this->entityManager);
  }

  /**
   * @covers ::getConfigStorage
   * @covers ::setConfigStorage
   */
  public function testGetConfigStorage() {
    $method_get = new \ReflectionMethod($this->configImporter, 'getConfigStorage');
    $method_get->setAccessible(TRUE);

    $extension = new Extension($this->randomMachineName(), $this->randomMachineName(), $this->randomMachineName());

    $this->moduleHandler->expects($this->atLeastOnce())
      ->method('getModule')
      ->willReturn($extension);

    $this->assertInstanceof(StorageInterface::class, $method_get->invoke($this->configImporter));

    $config_storage = $this->getMock(StorageInterface::class);
    $this->configImporter->setConfigStorage($config_storage);
    $this->assertSame($config_storage, $method_get->invoke($this->configImporter));
  }

  /**
   * @covers ::getImportableCurrencies
   * @covers ::getImportables
   */
  public function testGetImportableCurrencies() {
    $entity_type_id = $this->randomMachineName();

    $currency_code_a = $this->randomMachineName();
    $currency_a = $this->getMock(CurrencyInterface::class);

    $currency_code_b = $this->randomMachineName();
    $currency_b = $this->getMock(CurrencyInterface::class);

    $currency_code_c = $this->randomMachineName();
    $currency_data_c = [
      'id' => $currency_code_c,
    ];
    $currency_c = $this->getMock(CurrencyInterface::class);

    $stored_currencies = [
      $currency_code_a => $currency_a,
      $currency_code_b => $currency_b,
    ];

    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('create')
      ->with($currency_data_c)
      ->willReturn($currency_c);
    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('getEntityTypeId')
      ->willReturn($entity_type_id);
    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('loadMultiple')
      ->with(NULL)
      ->willReturn($stored_currencies);

    $prefix = 'currency.' . $entity_type_id . '.';
    $this->configStorage->expects($this->atLeastOnce())
      ->method('listAll')
      ->with($prefix)
      ->willReturn([$prefix . $currency_code_b, $prefix . $currency_code_c]);
    $this->configStorage->expects($this->once())
      ->method('read')
      ->with($prefix . $currency_code_c)
      ->willReturn($currency_data_c);

    $this->configImporter->setConfigStorage($this->configStorage);

    $importable_currencies = $this->configImporter->getImportableCurrencies();
    $this->assertSame([$currency_c], $importable_currencies);
  }

  /**
   * @covers ::getImportableCurrencyLocales
   * @covers ::getImportables
   */
  public function testGetImportableCurrencyLocales() {
    $entity_type_id = $this->randomMachineName();

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
      ->method('getEntityTypeId')
      ->willReturn($entity_type_id);
    $this->currencyLocaleStorage->expects($this->atLeastOnce())
      ->method('loadMultiple')
      ->with(NULL)
      ->willReturn($stored_currencies);

    $prefix = 'currency.' . $entity_type_id . '.';
    $this->configStorage->expects($this->atLeastOnce())
      ->method('listAll')
      ->with($prefix)
      ->willReturn([$prefix . $locale_b, $prefix . $locale_c]);
    $this->configStorage->expects($this->once())
      ->method('read')
      ->with($prefix . $locale_c)
      ->willReturn($currency_locale_data_c);

    $this->configImporter->setConfigStorage($this->configStorage);

    $importable_currencies = $this->configImporter->getImportableCurrencyLocales();
    $this->assertSame([$currency_locale_c], $importable_currencies);
  }

  /**
   * @covers ::importCurrency
   * @covers ::import
   */
  public function testImportCurrency() {
    $entity_type_id = $this->randomMachineName();

    $currency_code = $this->randomMachineName();
    $currency_data = [
      'id' => $currency_code,
    ];
    $currency = $this->getMock(CurrencyInterface::class);

    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('create')
      ->with($currency_data)
      ->willReturn($currency);
    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('getEntityTypeId')
      ->willReturn($entity_type_id);

    $this->configStorage->expects($this->once())
      ->method('read')
      ->with('currency.' . $entity_type_id . '.' . $currency_code)
      ->willReturn($currency_data);

    $this->configImporter->setConfigStorage($this->configStorage);

    $this->assertSame($currency, $this->configImporter->importCurrency($currency_code));
  }

  /**
   * @covers ::importCurrency
   * @covers ::import
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

    $this->configImporter->setConfigStorage($this->configStorage);

    $this->assertFalse($this->configImporter->importCurrency($currency_code));
  }

  /**
   * @covers ::importCurrencyLocale
   * @covers ::import
   */
  public function testImportCurrencyLocale() {
    $entity_type_id = $this->randomMachineName();

    $locale = $this->randomMachineName();
    $currency_locale_data = [
      'id' => $locale,
    ];
    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);

    $this->currencyLocaleStorage->expects($this->atLeastOnce())
      ->method('create')
      ->with($currency_locale_data)
      ->willReturn($currency_locale);
    $this->currencyLocaleStorage->expects($this->atLeastOnce())
      ->method('getEntityTypeId')
      ->willReturn($entity_type_id);

    $this->configStorage->expects($this->once())
      ->method('read')
      ->with('currency.' . $entity_type_id . '.' . $locale)
      ->willReturn($currency_locale_data);

    $this->configImporter->setConfigStorage($this->configStorage);

    $this->assertSame($currency_locale, $this->configImporter->importCurrencyLocale($locale));
  }

  /**
   * @covers ::importCurrencyLocale
   * @covers ::import
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

    $this->configImporter->setConfigStorage($this->configStorage);

    $this->assertFalse($this->configImporter->importCurrencyLocale($locale));
  }

}
