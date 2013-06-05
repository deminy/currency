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
  public function __construct(\Traversable $namespaces) {
    $annotation_namespaces = array(
      'Drupal\currency\Annotation' => drupal_get_path('module', 'currency') . '/lib',
    );
    $this->discovery = new AnnotatedClassDiscovery('currency/exchanger', $namespaces, $annotation_namespaces, 'Drupal\currency\Annotation\CurrencyExchanger');
    $this->discovery = new AlterDecorator($this->discovery, 'currency_exchanger');
    $this->discovery = new CacheDecorator($this->discovery, 'currency_exchanger');
    $this->factory = new DefaultFactory($this->discovery);
  }
}
