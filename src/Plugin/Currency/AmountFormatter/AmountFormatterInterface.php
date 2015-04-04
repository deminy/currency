<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface.
 */

namespace Drupal\currency\Plugin\Currency\AmountFormatter;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Language\LanguageInterface;
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
   * @param string $language_type
   *   One of the \Drupal\Core\Language\LanguageInterface\TYPE_* constants.
   *
   * return string
   * @param $language_type
   * @return
   */
  function formatAmount(CurrencyInterface $currency, $amount, $language_type = LanguageInterface::TYPE_CONTENT);
}
