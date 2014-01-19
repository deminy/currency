<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface.
 */

namespace Drupal\currency\Plugin\Currency\AmountFormatter;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\currency\Entity\CurrencyInterface;

/**
 * Defines a plugin type to format amounts.
 */
interface AmountFormatterInterface extends PluginInspectionInterface {

  /**
   * Formats an amount.
   *
   * @param \Drupal\currency\Entity\CurrencyInterface $currency
   *   The currency the amount is in.
   * @param string $amount
   *   A numeric string.
   *
   * return string
   */
  function formatAmount(CurrencyInterface $currency, $amount);
}
