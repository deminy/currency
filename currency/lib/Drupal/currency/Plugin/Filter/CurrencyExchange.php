<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\Filter\CurrencyExchange.
 */

namespace Drupal\currency\Plugin\Filter;

use Drupal\currency\Input;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a filter to exchange currencies.
 *
 * @Filter(
 *   id = "currency_exchange",
 *   module = "currency",
 *   title = @Translation("Currency exchange"),
 *   type = FILTER_TYPE_MARKUP_LANGUAGE
 * )
 */
class CurrencyExchange extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode, $cache, $cache_id) {
    return preg_replace_callback('/\[currency:([a-z]{3}):([a-z]{3})(.*?)\]/i', array($this, 'processCallback'), $text);
  }

  /**
   * Implements preg_replace_callback() callback.
   *
   * @see self::process()
   */
  function processCallback(array $matches) {
    $currency_code_from = $matches[1];
    $currency_code_to = $matches[2];
    $amount = str_replace(':', '', $matches[3]);
    if (strlen($amount) !== 0) {
      $amount = \Drupal::service('currency.input')->parseAmount($amount);
      // The amount is invalid, so return the token.
      if (!$amount) {
        return $matches[0];
      }
    }
    // The amount defaults to 1.
    else {
      $amount = 1;
    }

    $exchanger = \Drupal::service('currency.exchange_rate_provider');
    $rate = $exchanger->load($currency_code_from, $currency_code_to);
    if ($rate) {
      return bcmul($amount, $rate, CURRENCY_BCMATH_SCALE);
    }
    // The filter failed, so return the token.
    return $matches[0];
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return t('Use <code>[currency:from:to:amount]</code> to convert an amount of money from one currency to another. The <code>amount</code> parameter is optional and defaults to <code>1</code>. Example: <code>[currency:EUR:USD:100]</code>.');
  }

}