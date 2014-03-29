<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\views\filter\Currency.
 */

namespace Drupal\currency\Plugin\views\filter;

use Drupal\currency\Entity\Currency as CurrencyEntity;
use Drupal\views\Plugin\views\filter\InOperator;

/**
 * A Views filter handler to filter currencies by ISO 4217 code.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("currency")
 */
class Currency extends InOperator {

  /**
   * {@inheritdoc}
   */
  function getValueOptions() {
    if (is_null($this->value_options)) {
      $this->value_options = CurrencyEntity::options();
    }

    return $this->value_options;
  }
}
