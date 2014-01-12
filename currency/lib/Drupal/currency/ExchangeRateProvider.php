<?php

/**
 * @file
 * Contains \Drupal\currency\ExchangeRateProvider.
 */

namespace Drupal\currency;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Provides currency exchange rates through plugins.
 */
class ExchangeRateProvider implements ExchangeRateProviderInterface {

  /**
   * A configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory = NULL;

  /**
   * A currency exchanger plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $pluginManager = NULL;

  /**
   * Constructor.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $pluginManager
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   */
  function __construct(PluginManagerInterface $pluginManager, ConfigFactory $configFactory) {
    $this->pluginManager = $pluginManager;
    $this->configFactory = $configFactory;
  }

  /**
   * Loads the configuration.
   *
   * @return array
   *   Keys are currency_exchanger plugin names. Values are booleans that
   *   describe whether the plugins are enabled. Items are ordered by weight.
   */
  public function loadConfiguration() {
    $definitions = $this->pluginManager->getDefinitions();
    $configuration = $this->configFactory->get('currency.exchange_rate_provider')->get('exchangers') + array_fill_keys(array_keys($definitions), TRUE);

    return $configuration;
  }

  /**
   * Saves the configuration.
   *
   * @param array $configuration
   *   Keys are currency_exchanger plugin names. Values are booleans that
   *   describe whether the plugins are enabled. Items are ordered by weight.
   *
   * @return NULL
   */
  public function saveConfiguration(array $configuration) {
    $config = $this->configFactory->get('currency.exchange_rate_provider');
    $config->set('exchangers', $configuration);
    $config->save();
  }

  /**
   * Returns enabled currency exchanger plugins, sorted by weight.
   *
   * @return array
   */
  public function loadExchangers() {
    $names = array_keys(array_filter($this->loadConfiguration()));
    $plugins = array();
    foreach ($names as $name) {
      $plugins[$name] = $this->pluginManager->createInstance($name, array());
    }

    return $plugins;
  }

  /**
   * {@inheritdoc}
   */
  public function load($currency_code_from, $currency_code_to) {
    if ($currency_code_from == $currency_code_to) {
      return 1;
    }
    foreach ($this->loadExchangers() as $exchanger) {
      if ($rate = $exchanger->load($currency_code_from, $currency_code_to)) {
        return $rate;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $currency_codes) {
    $rates = array();

    // Set rates for identical source and destination currencies.
    foreach ($currency_codes as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $index => $currency_code_to) {
        if ($currency_code_from == $currency_code_to) {
          $rates[$currency_code_from][$currency_code_to] = 1;
          unset($currency_codes[$currency_code_from][$index]);
        }
      }
    }

    foreach ($this->loadExchangers() as $exchanger) {
      foreach ($exchanger->loadMultiple($currency_codes) as $currency_code_from => $currency_codes_to) {
        foreach ($currency_codes_to as $currency_code_to => $rate) {
          $rates[$currency_code_from][$currency_code_to] = $rate;
          // If we found a rate, prevent it from being looked up by the next exchanger.
          if ($rate) {
            $index = array_search($currency_code_to, $currency_codes[$currency_code_from]);
            unset($currency_codes[$currency_code_from][$index]);
          }
        }
      }
    }

    return $rates;
  }
}
