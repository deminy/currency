<?php

/**
 * @file
 * Contains \Drupal\currency\Annotation\CurrencyAmountFormatter.
 */

namespace Drupal\currency\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a currency amount formatter plugin definition.
 *
 * @Annotation
 */
class CurrencyAmountFormatter extends Plugin {

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

}
