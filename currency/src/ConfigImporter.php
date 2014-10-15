<?php

/**
 * @file
 * Contains \Drupal\currency\ConfigImporter.
 */

namespace Drupal\currency;

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a config importer.
 */
class ConfigImporter implements ConfigImporterInterface {

  /**
   * The config storage.
   *
   * @see self::getConfigStorage()
   * @see self::setConfigStorage()
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $configStorage;

  /**
   * The currency entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $currencyStorage;

  /**
   * The currency locale entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $currencyLocaleStorage;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The typed config manager.
   *
   * @var \Drupal\Core\Config\TypedConfigManagerInterface
   */
  protected $typedConfigManager;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface
   *   The module handler.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   The event dispatcher.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typed_config_manager
   *   THe typed config manager.
   * @param \Drupal\Core\Entity\EntityManagerInterface
   *   The entity manager.
   */
  public function __construct(ModuleHandlerInterface $module_handler, EventDispatcherInterface $event_dispatcher, TypedConfigManagerInterface $typed_config_manager, EntityManagerInterface $entity_manager) {
    $this->currencyStorage = $entity_manager->getStorage('currency');
    $this->currencyLocaleStorage = $entity_manager->getStorage('currency_locale');
    $this->eventDispatcher = $event_dispatcher;
    $this->moduleHandler = $module_handler;
    $this->typedConfigManager = $typed_config_manager;
  }

  /**
   * Gets the currency config storage.
   *
   * @return \Drupal\Core\Config\StorageInterface
   */
  protected function getConfigStorage() {
    if (!$this->configStorage) {
      $this->configStorage = new FileStorage($this->moduleHandler->getModule('currency')->getPath() . '/config/default/');
    }

    return $this->configStorage;
  }

  /**
   * Sets the config storage.
   *
   * @param \Drupal\Core\Config\StorageInterface
   */
  public function setConfigStorage(StorageInterface $storage) {
    $this->configStorage = $storage;
  }

  /**
   * Gets importable entities.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   */
  protected function getImportables(EntityStorageInterface $entity_storage) {
    $existing_entities = $entity_storage->loadMultiple();
    $entities = [];
    $prefix = 'currency.' . $entity_storage->getEntityTypeId() . '.';
    foreach ($this->getConfigStorage()->listAll($prefix) as $name) {
      if (!isset($existing_entities[substr($name, strlen($prefix))])) {
        $entities[] = $entity_storage->create($this->getConfigStorage()->read($name));
      }
    }

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function getImportableCurrencies() {
    return $this->getImportables($this->currencyStorage);
  }

  /**
   * {@inheritdoc}
   */
  public function getImportableCurrencyLocales() {
    return $this->getImportables($this->currencyLocaleStorage);
  }

  /**
   * Imports an entity.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   * @param string $entity_id
   *
   * @return \Drupal\Core\Entity\EntityInterface|false
   *   The imported entity or FALSE in case of errors.
   */
  protected function import(EntityStorageInterface $entity_storage, $entity_id) {
    $name = 'currency.' . $entity_storage->getEntityTypeId() . '.' . $entity_id;
    $entity = $entity_storage->create($this->getConfigStorage()->read($name));
    $entity->save();
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function importCurrency($currency_code) {
    return $this->import($this->currencyStorage, $currency_code);
  }

  /**
   * {@inheritdoc}
   */
  public function importCurrencyLocale($locale) {
    return $this->import($this->currencyLocaleStorage, $locale);
  }

}
