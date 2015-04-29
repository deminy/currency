<?php

/**
 * Contains \Drupal\currency\Plugin\Currency\OperationsProviderPluginManagerTrait.
 */

namespace Drupal\currency\Plugin\Currency;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;

/**
 * Implements \Drupal\currency\Plugin\Currency\OperationsProviderPluginManagerInterface.
 */
trait OperationsProviderPluginManagerTrait {

  /**
   * The class resolver.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface
   */
  protected $classResolver;

  /**
   * {@inheritdoc}
   */
  public function getOperationsProvider($plugin_id) {
    /** @var \Drupal\Component\Plugin\PluginManagerInterface|\Drupal\currency\Plugin\Currency\OperationsProviderPluginManagerTrait $this */
    $definition = $this->getDefinition($plugin_id);
    if ($definition) {
      if (isset($definition['operations_provider'])) {
        return $this->classResolver->getInstanceFromDefinition($definition['operations_provider']);
      }
      return NULL;
    }
    else {
      throw new PluginNotFoundException($plugin_id);
    }
  }

}
