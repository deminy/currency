<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\Filter\CurrencyExchange.
 */

namespace Drupal\currency\Plugin\Filter;

use Drupal\currency\Input;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a filter to format amounts.
 *
 * @Filter(
 *   cache = false,
 *   id = "currency_localize",
 *   module = "currency",
 *   title = @Translation("Currency amount formatting"),
 *   type = FILTER_TYPE_MARKUP_LANGUAGE
 * )
 */
class CurrencyLocalize extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode, $cache, $cache_id) {
    return preg_replace_callback('/\[currency-localize:([a-z]{3}):(.+?)\]/i', array($this, 'processCallback'), $text);
  }

  /**
   * Implements preg_replace_callback() callback.
   *
   * @see self::process()
   */
  function processCallback(array $matches) {
    $currency_code = $matches[1];
    $amount = \Drupal::service('currency.input')->parseAmount($matches[2]);
    // The amount is invalid, so return the token.
    if (!$amount) {
      return $matches[0];
    }
    $currency = entity_load('currency', $currency_code);
    if ($currency) {
      return $currency->format($amount);
    }
    // The currency code is invalid, so return the token.
    return $matches[0];
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return t('Use <code>[currency-localize:<strong>currency-code</strong>:<strong>amount</strong>]</code> to localize an amount of money. The <code>currency-code</code> and <code>amount</code> parameters are the ISO 4217 currency code and the actual amount to display. Example: <code>[currency-localize:EUR:99.95]</code>.');
  }

}