<?php

/**
 * @file
 * Contains \Drupal\currency\CurrencyServiceProvider.
 */

namespace Drupal\currency;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\currency\Container\MathCompilerPass;

/**
 * Provides Currency's service provider.
 */
class CurrencyServiceProvider implements ServiceProviderInterface  {

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new MathCompilerPass());
  }

}
