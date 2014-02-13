<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManager.
 */

namespace Drupal\currency\Plugin\Currency\AmountFormatter;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages amount formatter plugins.
 */
class AmountFormatterManager extends DefaultPluginManager implements AmountFormatterManagerInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a new class instance.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, LanguageManager $language_manager, ModuleHandlerInterface $module_handler, ConfigFactory $config_factory) {
    parent::__construct('Plugin/Currency/AmountFormatter', $namespaces, '\Drupal\currency\Annotation\CurrencyAmountFormatter');
    $this->alterInfo($module_handler, 'currency_amount_formatter');
    $this->setCacheBackend($cache_backend, $language_manager, 'currency_amount_formatter');
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultPluginId() {
    return $this->configFactory->get('currency.amount_formatter')
      ->get('plugin_id');
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultPluginId($plugin_id) {
    $this->configFactory->get('currency.amount_formatter')
      ->set('plugin_id', $plugin_id)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultPlugin() {
    return $this->createInstance($this->getDefaultPluginId());
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = array()) {
    if (!$this->getDefinition($plugin_id)) {
      $plugin_id = $this::FALLBACK_PLUGIN_ID;
    }
    return parent::createInstance($plugin_id, $configuration);
  }
}
