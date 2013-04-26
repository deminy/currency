<?php

/**
 * @file
 * Contains \Drupal\currency\Annotation\CurrencyExchanger.
 */

namespace Drupal\currency\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a currency exchanger annotation object.
 *
 * @Annotation
 */
class CurrencyExchanger extends Plugin {

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
   * Operations links (optional).
   *
   * @var array
   *   Keys are URL paths and values are link texts.
   */
  public $operations = array();

}
