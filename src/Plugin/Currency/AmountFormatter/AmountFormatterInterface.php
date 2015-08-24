<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface.
 */

namespace Drupal\currency\Plugin\Currency\AmountFormatter;

use BartFeenstra\Currency\CurrencyInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Defines a plugin type to format amounts.
 */
interface AmountFormatterInterface extends PluginInspectionInterface {

  /**
   * Formats an amount.
   *
   * @param \BartFeenstra\Currency\CurrencyInterface $currency
   *   The currency the amount is in.
   * @param string $amount
   *   A numeric string.
   * @param string $language_type
   *   One of the \Drupal\Core\Language\LanguageInterface\TYPE_* constants.
   *
   * return string
   * @param $language_type
   * @return
   */
  function formatAmount(CurrencyInterface $currency, $amount, $language_type = LanguageInterface::TYPE_CONTENT);
}
