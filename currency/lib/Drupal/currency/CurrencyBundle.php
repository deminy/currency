<?php

/**
 * @file
 * Contains \Drupal\currency\CurrencyBundle.
 */

namespace Drupal\currency;

use Drupal\Core\Cache\CacheFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The Currency dependency injection container.
 */
class CurrencyBundle extends Bundle {

  /**
   * Overrides parent::build().
   */
  public function build(ContainerBuilder $container) {
    $container->register('plugin.manager.currency.exchanger', 'Drupal\currency\Plugin\Type\ExchangerManager')
      ->addArgument('%container.namespaces%');
    $container->register('currency.exchanger', 'Drupal\currency\Plugin\currency\exchanger\Delegator');
  }
}
