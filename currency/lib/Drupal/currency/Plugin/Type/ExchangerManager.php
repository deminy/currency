<?php

/**
 * Contains \Drupal\currency\Plugin\Type\ExchangerManager.
 */

namespace Drupal\currency\Plugin\Type;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Core\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Core\Plugin\Discovery\AlterDecorator;
use Drupal\Core\Plugin\Discovery\CacheDecorator;

/**
 * Manages discovery and instantiation of currency exchanger plugins.
 *
 * @see \Drupal\block\BlockInterface
 */
class ExchangerManager extends PluginManagerBase {

  /**
   * Constructor.
   *
   * @param array $namespaces
   *   An array of paths keyed by their corresponding namespaces.
   */
  public function __construct(array $namespaces) {
    $this->discovery = new AnnotatedClassDiscovery('currency', 'exchanger', $namespaces);
    $this->discovery = new AlterDecorator($this->discovery, 'currency_exchanger');
    $this->discovery = new CacheDecorator($this->discovery, 'currency_exchanger');
    $this->factory = new DefaultFactory($this->discovery);
  }

  /**
   * Overrides parent::getDefinitions().
   */
  public function getDefinitions() {
    // Merge in default values.
    $definitions = parent::getDefinitions();
    foreach ($definitions as &$definition) {
      $definition += array(
        'description' => '',
        'operations' => array(),
      );
    }

    return $definitions;
  }
}
