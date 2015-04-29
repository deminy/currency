<?php

/**
 * Contains \Drupal\currency\Plugin\Currency\OperationsProviderPluginManagerInterface.
 */

namespace Drupal\currency\Plugin\Currency;

/**
 * Defines a plugin manager that can get operations providers for plugins.
 */
interface OperationsProviderPluginManagerInterface {

  /**
   * Gets a plugin's operations provider.
   *
   * @param string $plugin_id
   *
   * @return \Drupal\currency\Plugin\Currency\OperationsProviderInterface|null
   *   The operations provider or NULL if none is available.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getOperationsProvider($plugin_id);

}
