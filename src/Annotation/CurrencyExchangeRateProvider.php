<?php

/**
 * @file
 * Contains \Drupal\currency\Annotation\CurrencyExchangeRateProvider.
 */

namespace Drupal\currency\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a currency exchange rate provider plugin definition.
 *
 * @Annotation
 */
class CurrencyExchangeRateProvider extends Plugin {

  /**
   * The translated human-readable plugin name (optional).
   *
   * @var string
   */
  public $description = '';

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The translated human-readable plugin name.
   *
   * @var string
   */
  public $label;

  /**
   * The name of the class that provides plugin operations.
   *
   * The class must implement \Drupal\plugin\PluginOperationsProviderInterface.
   *
   * @var string
   */
  public $operations_provider;

}
