<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\ConfigImporterUnitTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\Core\Extension\Extension;
use Drupal\currency\ConfigImporter;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\ConfigImporter
 *
 * @group Currency
 */
class ConfigImporterUnitTest extends UnitTestCase {

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
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->configStorage = $this->getMock('\Drupal\Core\Config\StorageInterface');

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->currencyLocaleStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->eventDispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');

    $this->moduleHandler = $this->getMock('\Drupal\Core\Extension\ModuleHandlerInterface');

    $this->typedConfigManager = $this->getMock('\Drupal\Core\Config\TypedConfigManagerInterface');

    $map = [
      ['currency', $this->currencyStorage],
      ['currency_locale', $this->currencyLocaleStorage],
    ];
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->exactly(2))
      ->method('getStorage')
      ->willReturnMap($map);

    $this->configImporter = new ConfigImporter($this->moduleHandler, $this->eventDispatcher, $this->typedConfigManager, $entity_manager);
  }

  /**
   * @covers ::getConfigStorage
   * @covers ::setConfigStorage
   */
  public function testGetConfigStorage() {
    $method_get = new \ReflectionMethod($this->configImporter, 'getConfigStorage');
    $method_get->setAccessible(TRUE);

    $extension = new Extension($this->randomMachineName(), $this->randomMachineName());

    $this->moduleHandler->expects($this->atLeastOnce())
      ->method('getModule')
      ->willReturn($extension);

    $this->assertInstanceof('\Drupal\Core\Config\StorageInterface', $method_get->invoke($this->configImporter));

    $config_storage = $this->getMock('\Drupal\Core\Config\StorageInterface');
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
    $currency_a = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');

    $currency_code_b = $this->randomMachineName();
    $currency_b = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');

    $currency_code_c = $this->randomMachineName();
    $currency_data_c = [
      'id' => $currency_code_c,
    ];
    $currency_c = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');

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
    $currency_locale_a = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

    $locale_b = $this->randomMachineName();
    $currency_locale_b = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

    $locale_c = $this->randomMachineName();
    $currency_locale_data_c = [
      'id' => $locale_c,
    ];
    $currency_locale_c = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

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
    $currency = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');

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
   * @covers ::importCurrencyLocale
   * @covers ::import
   */
  public function testImportCurrencyLocale() {
    $entity_type_id = $this->randomMachineName();

    $locale = $this->randomMachineName();
    $currency_locale_data = [
      'id' => $locale,
    ];
    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

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

}
